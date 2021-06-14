<?php
namespace ExEss\Cms\FLW_Flows\Response;

class Form implements \JsonSerializable
{
    private string $id;

    private string $key_c;

    private string $type_c;

    private string $name;

    private array $cardList = [];

    public function __construct(string $id, string $type, string $key, string $name)
    {
        $this->id = $id;
        $this->type_c = $type;
        $this->key_c = $key;
        $this->name = $name;
    }

    public function setGroup(string $name, array $fields): Form
    {
        $group = new \stdClass();
        $group->fields = $fields;

        $this->cardList[$name] = $group;

        return $this;
    }

    public function getGroup(string $name): ?\stdClass
    {
        return $this->cardList[$name] ?? null;
    }

    /**
     * @return \stdClass[]
     */
    public function getGroups(): array
    {
        return $this->cardList;
    }

    public function jsonSerialize(): array
    {
        $variables = \get_object_vars($this);

        foreach ($variables['cardList'] as $name => $group) {
            $variables[$name] = $group;
        }
        $variables['cardList'] = \array_keys($variables['cardList']);

        return $variables;
    }

    /**
     * Fetches in what card a form field is present
     *
     */
    public function getCardFor(string $name): ?string
    {
        foreach ($this->getGroups() as $card => $group) {
            foreach ($group->fields as $field) {
                if (isset($field->id) && $field->id === $name) {
                    return $card;
                }
            }
        }

        return null;
    }

    /**
     * Checks if a form contains a certain card
     */
    public function hasCard(string $card): bool
    {
        return \array_key_exists($card, $this->getGroups());
    }

    /**
     * Adds a card to a form
     */
    public function addCard(string $card): void
    {
        if (false === $this->hasCard($card)) {
            $this->setGroup($card, []);
        }
    }

    /**
     * Checks if a form has contains a certain field
     */
    public function hasField(string $field): bool
    {
        return $this->getCardFor($field) !== null;
    }

    /**
     * Adds a form field to a form
     *
     * @param \stdClass|\ExEss\Cms\FLW_Flows\Response\Form\Field $field
     * @throws \InvalidArgumentException When the supplied field is not a real form field.
     */
    public function addField(string $card, \stdClass $field): void
    {
        if (false === $this->hasCard($card)) {
            $this->addCard($card);
        }

        $this->getGroup($card)->fields[] = $field;
    }

    /**
     * Removes a field from the form
     */
    public function removeField(string $name): void
    {
        $card = $this->getCardFor($name);

        if (null === $card) {
            return;
        }

        $group = $this->getGroup($card);
        foreach ($group->fields as $i => $field) {
            if (isset($field->id) && $field->id === $name) {
                unset($group->fields[$i]);
            }
        }
    }

    /**
     * Fetches a field in  a form
     *
     */
    public function getField(string $name): ?\ExEss\Cms\FLW_Flows\Response\Form\Field
    {
        $card = $this->getCardFor($name);

        if (null === $card) {
            return null;
        }

        foreach ($this->getGroup($card)->fields as $i => $field) {
            if (isset($field->id) && $field->id === $name) {
                return $field;
            }
        }

        return null;
    }

    public function getFieldCount(): int
    {
        $count = 0;
        foreach ($this->getGroups() as $group) {
            $count += \count($group->fields);
        }

        return $count;
    }

    public function getFieldList(): array
    {
        $fields = [];
        foreach ($this->getGroups() as $group) {
            foreach ($group->fields as $field) {
                $fields[] = $field->id;
            }
        }
        \sort($fields);

        return $fields;
    }

    /**
     * Returns all form fields.
     *
     * @return array|\stdClass[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->getGroups() as $group) {
            foreach ($group->fields as $field) {
                $fields[$field->id ?? \count($fields)] = $field;
            }
        }

        return $fields;
    }
}
