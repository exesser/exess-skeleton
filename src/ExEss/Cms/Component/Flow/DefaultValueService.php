<?php

namespace ExEss\Cms\Component\Flow;

use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Exception\ConfigInvalidException;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\MultiLevelTemplate\TextFunctionHandler;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class DefaultValueService
{
    private TextFunctionHandler $textFunctionsHandler;

    private array $translationTable;

    public function __construct(TextFunctionHandler $textFunctionsHandler)
    {
        $this->textFunctionsHandler = $textFunctionsHandler;
    }

    public function hasConditionalDefault(\stdClass $field): bool
    {
        return !empty($field->default)
            && DataCleaner::isJson($this->htmlEntityDecodeUtf8($field->default))
            && (!isset($field->type) || $field->type !== FlowFieldType::FIELD_TYPE_JSON);
    }

    /**
     * @throws ConfigInvalidException If a default value condition is incorrect.
     */
    public function getConditions(\stdClass $field): array
    {
        // make sure we have at least a "condition" and "value" key in each condition
        return \array_map(function ($el) use ($field) {
            if (!\is_array($el)) {
                throw new ConfigInvalidException("default value configuration for field {$field->id} is incorrect");
            }
            return \array_merge(['condition' => null, 'value' => null], $el);
        }, DataCleaner::jsonDecode($this->htmlEntityDecodeUtf8($field->default)));
    }

    public function isConditionMet(Model $model, string $condition): bool
    {
        return (new ExpressionLanguage())->evaluate($condition, [
            'model' => $model->toArray(),
            'default' => true,
        ]);
    }

    /**
     * @return mixed
     */
    public function getDefaultValueForField(\stdClass $field, Model $model, bool $handleOnlyConditionals)
    {
        if ($this->hasConditionalDefault($field)) {
            foreach ($this->getConditions($field) as $default) {
                if (empty($model->{$field->id})  && $this->isConditionMet($model, $default['condition'])) {
                    $value = $default['value'];

                    switch ($field->type) {
                        case FlowFieldType::FIELD_TYPE_DATE:
                        case FlowFieldType::FIELD_TYPE_DATETIME:
                            $dateTime = \date_create($value);
                            if ($dateTime) {
                                $value = $dateTime->format(
                                    $field->type === FlowFieldType::FIELD_TYPE_DATE
                                        ? Format::DB_DATE_FORMAT
                                        : Format::DB_DATETIME_FORMAT
                                );
                            }
                            break;
                    }
                    return $this->resolveFunctions($value, $field);
                }
            }

            return null;
        } elseif (isset($field->type) && $field->type === FlowFieldType::FIELD_TYPE_JSON) {
            $field->type = FlowFieldType::FIELD_TYPE_TEXTAREA;
        }

        if ($handleOnlyConditionals) {
            return null;
        }

        if (isset($field->default)) {
            return $this->resolveFunctions($field->default, $field);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function resolveFunctions($value, \stdClass $field)
    {
        if (
            \is_string($value) &&
            (
                !\property_exists($field, 'resolveFunctions') ||
                (\property_exists($field, 'resolveFunctions') && $field->resolveFunctions === true)
            )
        ) {
            return $this->textFunctionsHandler->resolveFunctions($value);
        }

        return $value;
    }

    /**
     * because conditional defaults can require a change after any model has changed, we need to keep checking
     * the conditional defaults until no changes are made anymore
     */
    public function resolveDefaults(Model $model, array $formGroups): void
    {
        if (!\count($formGroups)) {
            return;
        }

        $fields = \array_merge(...\array_map(
            function ($el) {
                return $el->fields;
            },
            \array_values($formGroups)
        ));

        do {
            $conditionalDefaultChanged = false;
            foreach ($fields as $field) {
                if (!empty($model->{$field->id}) || $model->{$field->id} === '0' || $model->{$field->id} === false) {
                    continue;
                }
                $newDefault = $this->getDefaultValueForField($field, $model, true);
                if (
                    $newDefault !== null
                    && $newDefault !== ''
                    && $newDefault !== $model->{$field->id}
                    && $model->offsetExists($field->id)
                ) {
                    $model->{$field->id} = $newDefault;
                    $conditionalDefaultChanged = true;
                }
            }
        } while ($conditionalDefaultChanged);
    }

    private function htmlEntityDecodeUtf8(string $string): string
    {
        $string = \preg_replace_callback(
            '~&#x0*([0-9a-f]+);~i',
            function (array $matches): string {
                return $this->code2utf(\hexdec($matches[1]));
            },
            $string
        );
        $string = \preg_replace_callback(
            '~&#0*([0-9]+);~',
            function (array $matches): string {
                return $this->code2utf($matches[1]);
            },
            $string
        );

        // replace literal entities
        if (!isset($this->translationTable)) {
            $this->translationTable = [];
            foreach (\get_html_translation_table(\HTML_ENTITIES) as $val => $key) {
                $this->translationTable[$key] = \utf8_encode($val);
            }
        }

        return \strtr($string, $this->translationTable);
    }

    private function code2utf(int $num): string
    {
        if ($num < 128) {
            return \chr($num);
        }
        if ($num < 2048) {
            return \chr(($num >> 6) + 192) . \chr(($num & 63) + 128);
        }
        if ($num < 65536) {
            return \chr(($num >> 12) + 224) . \chr((($num >> 6) & 63) + 128) . \chr(($num & 63) + 128);
        }
        if ($num < 2097152) {
            return \chr(($num >> 18) + 240) .
                \chr((($num >> 12) & 63) + 128) .
                \chr((($num >> 6) & 63) + 128) .
                \chr(($num & 63) + 128);
        }

        return '';
    }
}
