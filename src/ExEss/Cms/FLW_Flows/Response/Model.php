<?php
namespace ExEss\Cms\FLW_Flows\Response;

use ExEss\Cms\Helper\DataCleaner;

class Model implements \JsonSerializable, \ArrayAccess, \Countable, \Iterator
{
    /**
     * regex to split a field name on pipes not enclosed by braces
     * updated to also work on recursive braces
     * e.g.:
     * field_name(condition1|condition2(condition3|condition4)|inner_field
     *  - field_name(condition1|condition1)
     *  - inner_field
     */
    public const REGEX_PIPES_OUTSIDE_BRACES = '~(\\((?:[^()]++|(?1))*\\))(*SKIP)(*F)|\\|~';
    /**
     * same as above but for semicolons
     */
    public const REGEX_SEMICOLON_OUTSIDE_BRACES = '~(\\((?:[^()]++|(?1))*\\))(*SKIP)(*F)|;~';

    /**
     * regex to find outer matching braces
     */
    public const REGEX_OUTER_BRACES = '/\(([^()]|(?R))*\)/';

    /**
     * extracts the field and condition from a where clause in a definition
     * e.g.:
     *  - field_name<'%account('test')|id%'
     * will return matches "field_name" and "%account('test')|id%"
     */
    public const REGEX_FIELD_AND_VALUE = '/^[(]*(.+)=\s*[\\\'\"](.+)[\\\'\"][)]*/';

    /**
     * removes a single level of escaping when recursive escaping exists in a string
     */
    public const REGEX_REMOVE_SINGLE_LVL_ESCAPING = '/(?<!\\\\)\\\\/';

    protected array $properties = [];

    protected int $position = 0;

    /**
     * @param mixed $model
     *
     * @throws \InvalidArgumentException In case the passed argument is not an stdClass or array.
     */
    public function __construct($model = [])
    {
        if (!\is_array($model) && !$model instanceof \stdClass && !$model instanceof \Iterator) {
            throw new \InvalidArgumentException('Model must be an instance of stdClass or an iterator');
        }

        // this will initiate the magic setter for each property/array member
        foreach ($model as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Makes sure all properties are deep cloned
     */
    public function __clone()
    {
        foreach ($this->properties as $name => $value) {
            if ($value instanceof self) {
                $this->properties[$name] = clone $value;
            }
        }
    }

    /**
     * @param mixed $name
     */
    public function __isset($name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @throws \InvalidArgumentException When assigning an AbstractFatEntity to a property.
     */
    public function __set($name, $value): void
    {
        // make sure values are never nested stdClasses
        if (!$value instanceof self && ($value instanceof \stdClass || \is_array($value))) {
            $value = new self($value);
        }

        if ($value instanceof \JsonSerializable && !$value instanceof self) {
            $value = new self($value->jsonSerialize());
        }

        // @todo only allow instances of Model as objects in a Model

        $this->properties[$name] = $value;
    }

    public function __unset(string $name): void
    {
        unset($this->properties[$name]);
    }

    /**
     * @param mixed $name
     *
     * @return Model|bool|string|float|int|null
     */
    public function __get($name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->properties;
    }

    /**
     * Utility method to support older code that expects the model as an array
     */
    public function toArray(): array
    {
        return DataCleaner::jsonDecode(\json_encode($this));
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->properties);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->__get((string) $offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->__set((string) $offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return \count($this->properties);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->properties[\array_keys($this->properties)[$this->position]];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return \array_keys($this->properties)[$this->position];
    }

    /**
     * @return array|string[]
     */
    public function keys(): array
    {
        return \array_keys($this->properties);
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return \array_key_exists($this->position, \array_keys($this->properties));
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return mixed
     */
    public function last()
    {
        if (empty($this->properties)) {
            return null;
        }

        return \end($this->properties);
    }

    /**
     * match 'field', 'relation|field'
     * but not 'relation|field|otherField' or 'field|otherField'
     * also allow for pipes in $field so we can search for fields of particular relation:
     * match 'relation|subrelation|field' for 'subrelation|field'
     * also allow common '_c' suffix
     * supports also 'parent.relation|field' which will search for 'relation|field' in model['parent']
     */
    public function getFields(string $field): array
    {
        $keys = \explode('.', $field);
        $field = \array_pop($keys);
        $parentKeys = $keys;

        $searchIn = $this;
        foreach ($parentKeys as $key) {
            $searchIn = $searchIn->$key ?? new self();
        }

        $searchInKeys = \array_keys($searchIn->toArray());

        $matchingFields = \preg_grep('/(^|\|)' . \preg_quote($field, '/') . '(_c)?$/', $searchInKeys);

        $foundFields = [];
        foreach ($matchingFields as $matchingField) {
            $fieldKeys = \array_merge($parentKeys, [$matchingField]);
            $foundFields[\implode('.', $fieldKeys)] = $searchIn->$matchingField;
        }

        return $foundFields;
    }

    /**
     * @throws \UnexpectedValueException Multiple fields found in model for a specific field.
     */
    public function getField(string $field, bool $exactMatchIfPossible = false): array
    {
        $foundFields = $this->getFields($field);

        switch (\count($foundFields)) {
            case 1:
                return $foundFields;
            case 0:
                return [];
            default:
                if ($exactMatchIfPossible && \array_key_exists($field, $foundFields)) {
                    return [
                        $field => $foundFields[$field],
                    ];
                } elseif ($exactMatchIfPossible) {
                    return [];
                }

                throw new \UnexpectedValueException(\sprintf("multiple fields found in model for field '%s'", $field));
        }
    }

    /**
     * @param mixed $defaultKey
     * @param mixed $defaultValue
     */
    public function getFirstKeyAndValue(string $field, $defaultKey = '', $defaultValue = ''): array
    {
        $fields = $this->getFields($field);
        if (!empty($fields)) {
            $defaultValue = \reset($fields);
            $defaultKey = \key($fields);
        }

        return [$defaultKey, $defaultValue];
    }

    public function getFieldsFirstKeyAndValue(array $modelKeys): array
    {
        $fields = [];
        foreach ($modelKeys as $customKey => $modelKey) {
            [$key, $value] = $this->getFirstKeyAndValue($modelKey);
            $fields[$customKey]['key'] = $key;
            $fields[$customKey]['value'] = $value;
        }

        return $fields;
    }

    /**
     * Sets the value on a certain field.
     * We should have this method if you want to set a value without finding/getting the key first
     *
     * @param mixed $value
     * @throws \DomainException If this is set to true, an exception will be thrown when the field is not found.
     */
    public function setFieldValue(
        string $field,
        $value,
        bool $strict = false,
        bool $exactMatchIfPossible = false,
        bool $onlyIfExists = false
    ): void {

        $key = $this->findFieldKey($field, null, $exactMatchIfPossible);

        if ($key === null && $strict === true) {
            throw new \DomainException(\sprintf('Field %s was not found on the model', $field));
        }

        if ($key === null && $onlyIfExists) {
            return;
        }

        if ($key === null) {
            $key = $field;
        }

        $this->$key = $value;
    }

    /**
     * find a value by key or multiple keys
     * returns if fields are found for given key and all values are equal
     * if no values or different values are found for all of the keys, the $default is returned
     *
     * @param array|string $fields
     * @param mixed $default
     * @return mixed
     */
    public function findFieldValue($fields, $default = '')
    {
        if (!\is_array($fields)) {
            $fields = [$fields];
        }

        /** @var array $fields */
        foreach ($fields as $field) {
            $values = \array_unique($this->getFields($field), \SORT_REGULAR);
            if (\count($values) === 1) {
                return \array_values($values)[0];
            }
        }

        return $default;
    }

    /**
     * Verify if the field exists and has a non empty value
     *
     * @param mixed $field
     */
    public function hasNonEmptyValueFor($field, bool $exactMatchIfPossible = false): bool
    {
        if (!empty($this->$field)) {
            $fieldKey = $field;
        } else {
            $fieldKey = $this->findFieldKey($field, null, $exactMatchIfPossible);
        }

        if (!$fieldKey) {
            return false;
        }

        $fieldValue = $this->$fieldKey;

        if ($fieldValue instanceof self) {
            $fieldValue = $fieldValue->toArray();
        }

        return $fieldKey !== null && !empty($fieldValue);
    }

    /**
     * Checks if the model has a value for a certain field
     */
    public function hasValueFor(string $field): bool
    {
        if (true === $this->offsetExists($field)) {
            $key = $field;
        } else {
            $key = $this->findFieldKey($field);
        }

        $value = $this->{$key};

        if ($value instanceof self) {
            $value = $value->toArray();

            if (empty($value)) {
                return false;
            }
        }

        return !($value === null || $value === '');
    }

    /**
     * Check if the model has a field.
     */
    public function hasField(string $field, bool $exactMatchIfPossible = false): bool
    {
        $fieldKey = $this->findFieldKey($field, null, $exactMatchIfPossible);

        return !\is_null($fieldKey);
    }

    /**
     * Check if the model has multiple fields.
     */
    public function hasFields(array $fields, bool $exactMatchIfPossible = false): bool
    {
        foreach ($fields as $field) {
            if (!$this->hasField($field, $exactMatchIfPossible)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes a list of namespaces from the model
     * example: 'accounts|previous_inhabitants|record_type'
     * will be removed if 'accounts|previous_inhabitants' is in the excludeList
     */
    public function getCloneWithout(array $excludeList): Model
    {
        $tempModel = clone($this);

        foreach ($excludeList as $excludeItem) {
            $tempModel->clearNamespace($excludeItem);
        }

        return $tempModel;
    }

    /**
     * If the $fields param is a . separated list, it will look recursively for the value
     * For example connection.id will fetch the 'id' field in the connection node
     *
     * @param string|array $fields
     * @param mixed $default
     * @return mixed|Model
     */
    public function getFieldValue($fields, $default = '', bool $exactMatchIfPossible = false)
    {
        if (!\is_array($fields)) {
            $fields = [$fields];
        }

        /** @var array $fields */
        foreach ($fields as $field) {
            $foundFields = $this;
            // First check if the field is exacly there, before doing the magic '.' split
            // Since we have some models with autogenerated keys that include dots
            if (null !== $foundFields->offsetGet($field)) {
                return $foundFields->offsetGet($field);
            }
            $fieldParts = \explode('.', $field);
            foreach ($fieldParts as $fieldPart) {
                if (\is_array($foundFields)) {
                    $foundFields = \current($foundFields);
                }
                if ($foundFields instanceof Model) {
                    $foundFields = $foundFields->getField($fieldPart, $exactMatchIfPossible);
                }
            }

            if (!empty($foundFields)) {
                return \current($foundFields);
            }
        }

        return $default;
    }

    /**
     * find a values by key
     * returns key value pairs with the expanded field key
     * if key is not found in model it is left out of the result
     * if multiple keys are found in the model an exception is thrown
     *
     * @param string[] $fields
     */
    public function findFieldValues(array $fields): array
    {
        $values = [];
        foreach ($fields as $field) {
            $modelFields = $this->getField($field);

            if (\count($modelFields) === 1) {
                $field = \array_keys($modelFields)[0];
                $values[$field] = $field;
            }
        }

        return $values;
    }

    /**
     * @return mixed
     * @throws \UnexpectedValueException No key found in model for a field.
     */
    public function getFieldKey(string $search)
    {
        $field = $this->getField($search);

        if (empty($field)) {
            throw new \UnexpectedValueException(\sprintf("no key found in model for field '%s'", $search));
        }

        return \array_keys($field)[0];
    }

    public function findFieldKey(string $search, ?string $default = null, bool $exactMatchIfPossible = false): ?string
    {
        $field = $this->getField($search, $exactMatchIfPossible);

        if (empty($field)) {
            return $default;
        }

        return \array_keys($field)[0];
    }

    /**
     * Checks if a certain field is present in the model
     */
    public function fieldExists(string $field): bool
    {
        return $this->offsetExists($field) || !empty($this->findFieldKey($field));
    }

    public function findAllFieldKeys(string $field): array
    {
        return \array_keys($this->getFields($field));
    }

    public function findFieldKeys(array $fields, bool $exactMatchIfPossible = false): array
    {
        $keys = [];

        foreach ($fields as $field) {
            $modelFields = $this->getField($field, $exactMatchIfPossible);

            if (empty($modelFields)) {
                continue;
            }

            $keys[] = \array_keys($modelFields)[0];
        }

        return $keys;
    }

    public function findFirstKey(array $keys, string $default = '', bool $exactMatchIfPossible = false): string
    {
        foreach ($keys as $key) {
            $realFieldKey = $this->findFieldKey($key, 'some-default', $exactMatchIfPossible);
            if ($realFieldKey !== 'some-default') {
                return $realFieldKey;
            }
        }

        return $default;
    }

    /**
     * Clears all properties in a certain namespace
     */
    public function clearNamespace(string $namespace): void
    {
        foreach ($this->properties as $property => $value) {
            if (0 === \strpos($property, $namespace)) {
                unset($this->properties[$property]);
            }
        }
    }

    /**
     * @throws \InvalidArgumentException In case the model is not an array or stdClass.
     */
    public function getNamespace(string $namespace): array
    {
        if (empty($namespace)) {
            return [];
        }

        $children = [];
        foreach ($this->properties as $key => $value) {
            if (\strpos($key, $namespace) === 0) {
                $from = '/' . \preg_quote($namespace . '|', '/') . '/';
                $children[\preg_replace($from, '', $key, 1)] = $value;
            }
        }

        return $children;
    }

    /**
     * Get field id, when it's in an array/object and the key called key
     */
    public function getFieldId(string $field, string $default = ''): string
    {
        $id = $this->getFieldValue($field);

        if ($id instanceof self && $id->offsetExists(0) && $id[0]->offsetExists('key')) {
            return $id[0]->key;
        }

        return $default;
    }

    /**
     * Recursively merge the properties that do not exist in this model from the passed Model into this model
     */
    public function mergeNewProperties(Model $model): Model
    {
        foreach ($model as $property => $value) {
            if ($this->offsetExists($property)) {
                continue;
            }
            if ($value instanceof Model && isset($this->$property) && $this->$property instanceof Model) {
                $this->$property->mergeNewProperties($value);
            } else {
                $this->$property = $value;
            }
        }

        return $this;
    }

    /**
     * Recursively merge the values from the passed Model into this model
     */
    public function merge(Model $model): Model
    {
        foreach ($model as $property => $value) {
            $isset = isset($this->$property);

            if ($value instanceof Model && $isset && $this->$property instanceof Model) {
                $this->$property->merge($value);
            } else {
                $this->$property = $value;
            }
        }

        return $this;
    }

    /**
     * Returns the selected key of a SelectWithSearch or multiselectfield if only one item is selected.
     */
    public function getFirstKeyValue(): string
    {
        if ($this->count() === 1) {
            if ($this->offsetExists(0) && $this->offsetGet(0)->offsetExists('key')) {
                return $this->offsetGet(0)->offsetGet('key');
            }
        }

        return '';
    }

    public function substituteFieldKey(string $search, string $substition): string
    {
        return \substr($this->getFieldKey($search), 0, -\strlen($search)) . $substition;
    }
}
