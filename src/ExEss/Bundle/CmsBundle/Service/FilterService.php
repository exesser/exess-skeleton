<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\QueryBuilder;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Doctrine\Type\FilterFieldType;
use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\Filter;
use ExEss\Bundle\CmsBundle\Entity\FilterField;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Entity\SecurityGroup;
use ExEss\Bundle\CmsBundle\Component\Flow\Builder\EnumFieldBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterService
{
    public const CURRENT_USER_ID = '%current_user_id%';
    public const CURRENT_PRIMARY_GROUP_ID = '%current_primary_group_id%';
    public const CURRENT_DEALER_ID = '%current_dealer_id%';

    private Security $security;
    private EnumFieldBuilder $enumFieldBuilder;
    private TranslatorInterface $translator;

    public function __construct(
        Security $security,
        EnumFieldBuilder $enumFieldBuilder,
        TranslatorInterface $translator
    ) {
        $this->security = $security;
        $this->enumFieldBuilder = $enumFieldBuilder;
        $this->translator = $translator;
    }

    public function addQuickSearchConditions(
        string $alias,
        QueryBuilder $qb,
        array $quickSearchFields,
        string $value
    ): void {
        $expr = $qb->expr();

        $clauses = [];
        foreach ($quickSearchFields as $fieldKey) {
            $fieldDbKey = "$alias.$fieldKey";
            $clauses[] = $expr->like($fieldDbKey, "'%$value%'");
        }

        if (!empty($clauses)) {
            $qb->andWhere(\implode(' OR ', $clauses));
        }
    }

    /**
     * @param ArrayCollection|FilterField[] $listFilters
     */
    public function addFilterConditions(
        string $alias,
        QueryBuilder $qb,
        ArrayCollection $listFilters,
        array $requestFilters
    ): void {
        $expr = $qb->expr();

        foreach ($requestFilters as $fieldKey => $fieldConditions) {
            $fieldDbKey = "$alias.$fieldKey";

            foreach ($fieldConditions as $condition) {
                if (
                    !isset($condition['value'])
                    || $condition['value'] === ''
                    || (\is_array($condition['value']) && empty($condition['value']))
                ) {
                    continue;
                }

                // disallow virtual filter fields, it MUST exist on the list
                $filterField = null;
                if (isset($condition['fieldId'])) {
                    /** @var FilterField $filterField */
                    $filterField = $listFilters->matching(
                        (Criteria::create())
                            ->where(Criteria::expr()->eq('id', $condition['fieldId']))
                    )->current();
                }
                if (!$filterField) {
                    throw new \InvalidArgumentException("No filter field found for " . \json_encode($condition));
                }

                $operator = $filterField->getOperator();

                if ($condition['value'] === self::CURRENT_USER_ID) {
                    $condition['value'] = $this->security->getCurrentUser()->getId();
                    $condition['key'] = $this->security->getCurrentUser()->getName();
                } elseif ($condition['value'] === self::CURRENT_PRIMARY_GROUP_ID) {
                    $primaryGroup = $this->security->getPrimaryGroup();
                    if ($primaryGroup instanceof SecurityGroup) {
                        $condition['value'] = $primaryGroup->getId();
                        $condition['key'] = $primaryGroup->getName();
                    }
                }
                if ($filterField->getType() === FilterFieldType::SELECT_WITH_SEARCH) {
                    $condition['value'] = \array_column($condition['value'], 'key');
                }

                if (\strpos($fieldKey, '_I_') !== false) {
                    $operator = 'SQL';
                }

                // If we have been given a list with more then 10 items, switch to IN, instead of =
                if (
                    $operator === '='
                    && \is_string($condition['value'])
                    && (\substr_count($condition['value'], ';') + 1 > 10)
                ) {
                    $operator = 'IN';
                    $condition['value'] = \explode(';', $condition['value']);
                }

                if (\is_array($condition['value'])) {
                    if ($operator !== 'SQL') {
                        $operator = 'IN';
                    }
                }

                switch ($operator) {
                    case 'IN':
                        $qb->andWhere($expr->in($fieldDbKey, $condition['value']));
                        break;
                    case 'SQL':
                        if (!$filterField || !isset($condition['fieldId'])) {
                            throw new \InvalidArgumentException(
                                "Missing fieldId or filter field not linked to list"
                            );
                        }
                        $joins = \explode('_I_', $fieldKey);
                        $fieldDbKey = \array_pop($joins);
                        $joinAlias = $alias;
                        foreach ($joins as $join) {
                            $qb->join(
                                "$joinAlias.$join",
                                $joinAlias = \str_replace('.', '', "$joinAlias.$join")
                            );
                        }

                        if (\is_array($condition['value'])) {
                            $condition['value'] = \implode(', ', $condition['value']);
                        }
                        $sql =\trim(
                            \str_replace(
                                '####',
                                $condition['value'],
                                $filterField->getFieldSql(),
                            )
                        );
                        $qb->andWhere("$joinAlias.$fieldDbKey $sql");
                        break;
                    case '<':
                        $qb->andWhere($expr->gt($fieldDbKey, $condition['value']));
                        break;
                    case '>':
                        $qb->andWhere($expr->lt($fieldDbKey, $condition['value']));
                        break;
                    case '>=':
                        $qb->andWhere($expr->gte($fieldDbKey, $condition['value']));
                        break;
                    case '<=':
                        $qb->andWhere($expr->lte($fieldDbKey, $condition['value']));
                        break;
                    case '=':
                        $qb->andWhere($expr->eq($fieldDbKey, $expr->literal($condition['value'])));
                        break;
                    case 'LIKE':
                    default:
                        $clauses = [];
                        foreach (\explode(';', $condition['value']) as $value) {
                            $clauses[] = $expr->like($fieldDbKey, $expr->literal("$value%"));
                        }
                        $qb->andWhere(\implode(' OR ', $clauses));
                }
            }
        }
    }

    public function replaceDefaultValues(array $defaultFilters): array
    {
        foreach ($defaultFilters as $key => $value) {
            if (\is_array($value)) {
                $defaultFilters[$key] = $this->replaceDefaultValues($value);
                continue;
            }

            switch ($value) {
                case static::CURRENT_USER_ID:
                    $user = $this->security->getCurrentUser();
                    $defaultFilters[] = [
                        'key' => $user->getId(),
                        'label' => $user->getUserName(),
                    ];
                    break;
                case static::CURRENT_PRIMARY_GROUP_ID:
                    $primaryGroup = $this->security->getPrimaryGroup();
                    $defaultFilters[] = [
                        'key' => $primaryGroup->getId(),
                        'label' => $primaryGroup->getName(),
                    ];
                    unset($defaultFilters[$key]);
                    break;
            }
        }

        return $defaultFilters;
    }

    public function getFilters(ListDynamic $list): array
    {
        $filters = $this->generateFilterModelAndForm($list->getFilter());
        $filters['model'] = $this->applyListDefaultFiltersOnModel(
            $filters['model'],
            $list->getDefaultFilterValues()
        );

        return $filters;
    }

    public function generateFilterModelAndForm(Filter $filter, bool $addFilterFieldId = true): array
    {
        $form = [];
        $model = [];
        foreach ($filter->getGroups() as $group) {
            $fieldsForForm = [];
            foreach ($group->getFields() as $field) {
                $fieldForForm = $this->getFieldDetailsForForm($field);
                $fieldsForForm[] = $fieldForForm;
                [$fieldForFormNameDB, $fieldForFormKey, $value] = \explode('.', $fieldForForm['id'], 3);
                $model[$fieldForFormNameDB][$fieldForFormKey] = [
                    $value => \in_array(
                        $fieldForForm['type'],
                        [
                            FilterFieldType::ENUM,
                            FilterFieldType::CHECKBOX_GROUP,
                            FilterFieldType::TOGGLE_GROUP,
                            FilterFieldType::SELECT_WITH_SEARCH,
                        ]
                    ) ? [] : '',
                    'operator' => $fieldForForm['operator'],
                ];

                if ($addFilterFieldId) {
                    $model[$fieldForFormNameDB][$fieldForFormKey]['fieldId'] = $field->getId();
                }
            }

            $form[] = [
                'fields' => $fieldsForForm,
                'name' => $group->getName(),
                'sort' => $group->getSort(),
            ];
        }

        return [
            'model' => $model,
            'fieldGroups' => $form,
        ];
    }

    private function applyListDefaultFiltersOnModel(array $model, ?array $defaultFilterValues): array
    {
        if (empty($defaultFilterValues)) {
            return $model;
        }

        $listModel = $this->replaceDefaultValues($defaultFilterValues);

        foreach ($model as $fieldNameDB => $fields) {
            foreach ($fields as $fieldKey => $field) {
                foreach ($field as $fieldItemKey => $fieldItemValue) {
                    if (!empty($listModel[$fieldNameDB][$fieldKey][$fieldItemKey])) {
                        $model[$fieldNameDB][$fieldKey][$fieldItemKey] =
                            $listModel[$fieldNameDB][$fieldKey][$fieldItemKey];
                    }
                }
            }
        }

        return $model;
    }

    private function getFieldDetailsForForm(FilterField $field): array
    {
        $fieldDetails = [
            'id' => \str_replace('|', '_I_', $field->getFieldKey()) .
                (\strpos($field->getFieldKey(), '.') === false ? '.default' : '') .
                '.value',
            'label' => $this->translator->trans($field->getLabel(), [], TranslationDomain::LIST_FILTER),
            'type' => $field->getType(),
            'operator' => $field->getOperator(),
        ];

        if (!empty($fieldOptions = $field->getFieldOptions())) {
            foreach ($fieldOptions['enumValues'] ?? [] as &$enum) {
                $enum['value'] = $this->translator->trans($enum['value'], [], TranslationDomain::GUIDANCE_ENUM);
            }
            $fieldDetails = \array_merge($fieldDetails, $fieldOptions);
        }

        if (!empty($listName = $field->getFieldOptionsEnumKey())) {
            $fieldDetails['enumValues'] = $this->enumFieldBuilder->getEnumRecordsForType(Type::getType($listName));
        }

        return $fieldDetails;
    }
}
