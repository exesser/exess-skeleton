<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow;

use Doctrine\Common\Collections\Collection;
use ExEss\Bundle\CmsBundle\Dictionary\Model\Dwp;
use ExEss\Bundle\CmsBundle\Doctrine\Type\FlowFieldType;
use ExEss\Bundle\CmsBundle\Entity\FlowField;
use stdClass;

class Field
{
    private int $maxFileSize;

    public function __construct(int $maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * @param Collection|FlowField[] $fields
     */
    public function getFieldGroupsFromFields(Collection $fields, ?string $flowAction = null): array
    {
        $fieldsGroups = [];
        foreach ($fields as $field) {
            $currentFieldGroup = $field->getFieldGroup();
            $fieldsGroups[$currentFieldGroup] = $fieldsGroups[$currentFieldGroup] ?? [];

            $fieldObject = new stdClass();

            if ($field->getType() === FlowFieldType::FIELD_TYPE_CUSTOM) { //This is a major exception !
                $fieldsGroups[$currentFieldGroup][] = (object) $field->getCustom();
                continue;
            }

            $fieldObject->guid = $field->getId();
            $fieldObject->required = $field->isRequired();

            $fieldObject->id = $field->getFieldId();
            $fieldObject->label = $field->getLabel();
            $fieldObject->type = $field->getType();
            $fieldObject->auto_select_suggestions = $field->getAutoSelectSuggestions();
            $fieldObject->noBackendInteraction = $field->getNoBackendInteraction();
            if (!empty($overwriteValue = $field->getOverwriteValue())) {
                $fieldObject->overwrite_value = $overwriteValue;
            }
            if (!empty($module = $field->getModule())) {
                $fieldObject->module = $module;
            }
            if (!empty($orientation = $field->getOrientation())) {
                $fieldObject->orientation = $orientation;
            }
            if (!empty($moduleField = $field->getModuleField())) {
                $fieldObject->moduleField = $moduleField;
            }
            if (!empty($hideExpression = $field->getHideExpression())) {
                $fieldObject->hideExpression = $hideExpression;
            }
            if (!empty($fieldExpression = $field->getFieldExpression())) {
                $fieldObject->fieldExpression = $fieldExpression;
            }
            if (!empty($valueExpression = $field->getValueExpression())) {
                $fieldObject->valueExpression = $valueExpression;
            }
            if ($field->getHasBorder() === true) {
                $fieldObject->hasBorder = true;
            }

            $fieldObject->default = $field->getDefault();
            $fieldObject->readonly = $field->getReadOnly() || $flowAction === 'readOnly';

            if (!empty($disableExpressions = $field->getDisableExpression())) {
                $properties = new stdClass();
                $properties->{'templateOptions.disabled'} = $disableExpressions;
                $fieldObject->expressionProperties = $properties;
            }

            if ($field->getType() === FlowFieldType::FIELD_TYPE_ENUM) {
                if (!empty($generateByServer = $field->getGenerateByServer())) {
                    $fieldObject->generateByServer = $generateByServer;
                }
                if (!empty($generateType = $field->getGeneratedType())) {
                    $fieldObject->generateType = $generateType;
                }
                if ($multiple = $field->isMultiple()) {
                    $fieldObject->multiple = $multiple;
                }

                if (!empty($enumValues = $field->getEnumValues())) {
                    $fieldObject->enumValues = $enumValues;
                }
            }
            if (
                $field->getType() === FlowFieldType::FIELD_TYPE_INPUT_FIELD_GROUP
                && $field->getFieldId() === Dwp::CONTACT_PERSON_GROUP
            ) {
                $firstName = new stdClass();
                $firstName->id = 'first_name';
                $firstName->label = 'First name';
                $firstName->type = 'resizing-input';

                $lastName = new stdClass();
                $lastName->id = 'last_name';
                $lastName->label = 'Last name';
                $lastName->type = 'resizing-input';

                if ($field->getHasBorder() === true) {
                    $firstName->hasBorder = true;
                    $lastName->hasBorder = true;
                }

                $fieldObject->fields = [
                    $firstName,
                    $lastName,
                ];
            }

            foreach ($field->getCustom() ?? [] as $fieldCustomPropertyKey => $fieldCustomPropertyValue) {
                $fieldObject->{$fieldCustomPropertyKey} = $fieldCustomPropertyValue;
            }

            if ($field->getType() === FlowFieldType::FIELD_TYPE_LABEL_AND_ACTION) {
                $flwAction = $field->getFlowAction();
                $fieldObject->action = [
                    'id' => $flwAction && !empty($flwAction->getGuid()) ? $flwAction->getGuid() : null
                ];
                if (!empty($extraActionParameters = $field->getActionJson())) {
                    $fieldObject->action = \array_merge($fieldObject->action, $extraActionParameters);
                }
            }

            if ($field->getType() === FlowFieldType::FIELD_TYPE_HASHTAG_TEXT) {
                $fieldObject->datasourceName = $fieldObject->id;
            }

            if ($field->getType() === FlowFieldType::FIELD_TYPE_UPLOAD) {
                $fieldObject->maxFileSizeMB = $this->maxFileSize;
            }

            $fieldsGroups[$currentFieldGroup][] = $fieldObject;
        }

        return $fieldsGroups;
    }
}
