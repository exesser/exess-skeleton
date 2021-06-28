<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Flow\Handler;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\SecurityGroup;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Helper\DataCleaner;

class ModelConverter extends AbstractSaveHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function shouldHandle(FlowData $data): bool
    {
        return !empty($data->getFlow()->getBaseObject());
    }

    protected function doHandle(FlowData $data): void
    {
        $data->setConvertedModel(
            $this->explodeModel($data->getModel(), $data->getFlow()->isExternal())
        );
    }

    /**
     * converts pipe separated key data model to array of modules by ModuleName
     */
    private function explodeModel(Model $model, bool $external): array
    {
        $modules = [];

        // determine base module
        $baseModule = $model['baseModule'] ?? null;
        if ($baseModule) {
            $modules['baseModule'] = $baseModule;
            unset($model['baseModule']);
        }

        foreach ($model as $key => $val) {
            $tempModule = $this->createModuleStructure($key, $model, $baseModule);
            //Remove the stdClasses since they cause the array_replace_recursive to malfunction
            $tempModule = DataCleaner::jsonDecode(\json_encode($tempModule));
            // in case this is belongs on the baseModule (key already exists), put it there
            $firstKey = \key($tempModule);
            if (isset($modules[$baseModule][$firstKey])) {
                $modules[$baseModule] = \array_replace_recursive($modules[$baseModule], $tempModule);
            } else {
                $modules = \array_replace_recursive($modules, $tempModule);
            }
        }

        // Move the entities we are updating to the front
        \uksort($modules, function ($moduleName1, $moduleName2) use ($modules) {

            if ($moduleName1 === Dwp::DWP) {
                return 1;
            }

            if ($moduleName2 === Dwp::DWP) {
                return -1;
            }

            $moduleValue1 = $modules[$moduleName1];
            $moduleValue2 = $modules[$moduleName2];

            if (isset($moduleValue1['id'], $moduleValue2['id'])) {
                return 0;
            }
            if (isset($moduleValue1['id'])) {
                return -1;
            }
            return 1;
        });

        if ($baseModule === null) {
            return $modules;
        }

        if ($baseModule) {
            $modules['baseModule'] = $baseModule;
        }

        if ($external) {
            return $modules;
        }

        $baseMetaData = $this->em->getClassMetadata($baseModule);

        // Move the dependant entities to the front so we save them first.
        // For example we want to first save the list cells and only after that the list
        \uksort($modules, function ($moduleName1, $moduleName2) use ($modules, $baseMetaData, $baseModule) {

            $moduleValue1 = $modules[$moduleName1];
            $moduleValue2 = $modules[$moduleName2];

            if (empty($moduleValue1['id']) && $moduleName1 === $baseModule) {
                return -1;
            }

            // In other words, if the id field is not-resent or empty then ...
            if (empty($moduleValue2['id']) && $moduleName2 === $baseModule) {
                return 1;
            }

            if ($moduleName1 !== $moduleName2
                && isset($moduleValue1['id'], $moduleValue2['id'])
            ) {
                if ($baseMetaData->hasAssociation($moduleName1)
                    && $baseMetaData->getAssociationTargetClass($moduleName1) === SecurityGroup::class
                ) {
                    // these should always be processed last
                    return 1;
                }

                if ($moduleName1 === $baseModule) {
                    if ($baseMetaData->isSingleValuedAssociation($moduleName2)) {
                        return -1;
                    }
                    return 1;
                } elseif ($moduleName2 === $baseModule) {
                    if ($baseMetaData->isSingleValuedAssociation($moduleName1)) {
                        return -1;
                    }
                    return 1;
                }
            }

            return 0;
        });

        return $modules;
    }

    /**
     * If the field does not have a key then it is a base module field so grab the base module and create an array
     * else call the explodeModelField function to create a multi-dimensional array.
     */
    private function createModuleStructure(string $field, Model $model, ?string $baseModule = null): array
    {
        $result = [];
        $keys = \preg_split(Model::REGEX_PIPES_OUTSIDE_BRACES, $field);

        if (\count($keys) !== 1) {
            return $this->explodeModelField($model, $field);
        }

        $key = $keys[0];
        if (\strpos($key, '(')) {
            $relationshipName = \strstr($key, '(', true);
            $result[$relationshipName] = $model[$key];
        } elseif (\strpos($key, '{')) {
            $relationshipName = \strstr($key, '{', true);
            $result[$relationshipName] = $model[$key];
        } else {
            $result[$baseModule][$key] = $model[$key];
        }

        return $result;
    }

    /**
     * Converts pipe separated key to multidimensional array.
     * Note: using where-clauses works, but all the following items after the where-clause item will be set as
     *  object properties instead of a further nested array
     */
    private function explodeModelField(Model $model, string $field): array
    {
        $whereKeyValuePairs = [];
        $arr = [];
        $ref = &$arr;
        $lastKey = null;

        $keys = \preg_split(Model::REGEX_PIPES_OUTSIDE_BRACES, $field);
        while ($key = \array_shift($keys)) {
            $matches = [];
            //check for a whereClause
            $whereSearch = \preg_split(Model::REGEX_OUTER_BRACES, $key);
            if (\count($whereSearch) === 2 && $whereSearch[1] === '') {
                \preg_match(Model::REGEX_OUTER_BRACES, $key, $matches);
                //the key must be without the whereClause
                $key = $whereSearch[0];
                //determine the key-value pairs
                $whereKeyValuePairs = $this->explodeFieldWhereClause($matches[0]);
            }

            //build the array or set the object property (when where-clause was used)
            if ($ref instanceof \stdClass) {
                $ref->$key = null;
                $ref = &$ref->$key;
            } else {
                $ref = &$ref[$key];
            }

            //if we have key-value pairs: create an object so the properties can be set
            if (\count($whereKeyValuePairs) > 0) {
                $ref[$matches[0]] = new \stdClass();
                $ref = &$ref[$matches[0]];
                //add the key-value pairs as properties to the created object
                foreach ($whereKeyValuePairs as $whereKey => $whereValue) {
                    $ref->$whereKey = \preg_replace(Model::REGEX_REMOVE_SINGLE_LVL_ESCAPING, '', $whereValue);
                }
                //reset the key-value pairs again
                $whereKeyValuePairs = [];
            }

            //set the last key
            $lastKey = $key;
        }

        //set the fields value
        if ($ref instanceof \stdClass) {
            $ref->$lastKey = $model[$field];
        } else {
            $ref = $model[$field];
        }

        return $arr;
    }

    /**
     * Explode the whereClause from a guidanceField to an array with key-value pairs.
     * Supported examples:
     *  (type='legal')
     *  (type='legal';channel='post')
     */
    private function explodeFieldWhereClause(string $whereClause): array
    {
        $whereClause = \trim($whereClause, '()');
        $exploded = [];

        //explode on the semicolon
        $whereItems = \preg_split(Model::REGEX_SEMICOLON_OUTSIDE_BRACES, $whereClause);

        //each element should now be in the format key='value'
        foreach ($whereItems as $whereItem) {
            $matches = [];

            if (\preg_match(Model::REGEX_FIELD_AND_VALUE, $whereItem, $matches)) {
                $key = \trim($matches[1]);
                $value = $matches[2];

                $exploded[$key] = $value;
            }
        }
        return $exploded;
    }
}
