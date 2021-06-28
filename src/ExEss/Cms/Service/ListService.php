<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ExEss\Cms\Controller\ListDynamic\Body\ListBody;
use ExEss\Cms\CRUD\Config\CrudMetadata;
use ExEss\Cms\Dictionary\Format;
use ExEss\Cms\Doctrine\Type\CellType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Entity\ListSortingOption;
use ExEss\Cms\Acl\AclService;
use ExEss\Cms\Api\V8_Custom\Repository\ListHandler;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Entity\SecurityGroup;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListHeader;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListResponse;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListRow;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListRowCell;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListTopBarButton;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListTopBarSorting;
use ExEss\Cms\Component\ExpressionParser\ParserService;
use ExEss\Cms\Component\ExpressionParser\Parser\ExpressionGroup;
use ExEss\Cms\Component\ExpressionParser\Parser\ExpressionParserOptions;
use ExEss\Cms\Component\ExpressionParser\Parser\PathResolverOptions;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListService
{
    private const DEFAULT_PAGE_SIZE = 10;
    private const MAX_PER_PAGE = 100;

    private ParserService $parserService;

    private ListHandler $listHandler;

    private ActionService $actionService;

    private TranslatorInterface $translator;

    private Security $security;

    private AclService $aclService;

    private EntityManagerInterface $em;

    private FilterService $filterService;

    public function __construct(
        ParserService $parserService,
        ListHandler $listHandler,
        ActionService $actionService,
        TranslatorInterface $translator,
        Security $security,
        AclService $aclService,
        EntityManagerInterface $em,
        FilterService $filterService
    ) {
        $this->parserService = $parserService;
        $this->listHandler = $listHandler;
        $this->actionService = $actionService;
        $this->translator = $translator;
        $this->security = $security;
        $this->aclService = $aclService;
        $this->em = $em;
        $this->filterService = $filterService;
    }

    /**
     * @throws \DomainException When the baseFatEntity is not an instance of AbstractFatEntity
     * or the table doesn't exist.
     */
    public function getList(ListDynamic $list, ListBody $params, ?string $combinedListKey = null): DynamicListResponse
    {
        $response = new DynamicListResponse();

        $pageSize = $list->getItemsPerPage() ?? self::DEFAULT_PAGE_SIZE;

        // make sure that the number of items displayed per page
        // is not greater than the max we support
        if ($pageSize > self::MAX_PER_PAGE) {
            $pageSize = self::MAX_PER_PAGE;
        }

        $response->settings->title = $this->translator->trans($list->getTitle(), [], TranslationDomain::LIST_TITLE);
        $response->settings->displayFooter = $list->isDisplayFooter();
        $response->settings->responsive = $list->isResponsive();
        $response->settings->quickSearch = $list->isQuickSearch();

        //We pass along the id of the parent so the row actions still have access to it later on.
        $response->settings->actionData = new \stdClass();
        $response->settings->actionData->parentId = $params->getRecordId();
        $response->settings->actionData->parentType = $params->getRecordType();

        foreach ($params->getExtraActionData() as $key => $value) {
            $response->settings->actionData->$key = $value;
        }

        $this->fillTopBarOnList($list, $params, $response);

        $response->headers = $this->getHeadingsForList($list, $params->needsExportToCSV());
        $response->pagination->page = $params->getPage();

        if ($list->getExternalObject() && $list->isCombined()) {
            $this->getCombinedList(
                $list,
                $response,
                $params,
                $pageSize
            );
        } else {
            $queryBuilder = null;
            $metadata = null;
            if (!$list->isExternal()) {
                $metadata = $this->em->getClassMetadata($list->getBaseObject());
                if (!$list->isCombined()) {
                    $queryBuilder = $this->generateQueryBuilder(
                        $metadata,
                        $list,
                        $params,
                        $combinedListKey,
                        $response
                    );
                }
            }

            if ($params->needsOnlyRecordCount()) {
                $response->pagination->size = $pageSize;
                if ($list->getExternalObject()) {
                    $this->fetchRowsForList(
                        null,
                        $pageSize,
                        $list,
                        $params,
                        null,
                        $response,
                        $combinedListKey
                    );
                } else {
                    $response->pagination->total = \count(new Paginator($queryBuilder));
                }

                $response->pagination->pages = (int) \ceil($response->pagination->total / $pageSize);
            } else {
                //Pagination
                $response->pagination->size = $pageSize;

                $this->fetchRowsForList(
                    $metadata,
                    $pageSize,
                    $list,
                    $params,
                    $queryBuilder,
                    $response,
                    $combinedListKey
                );
            }
        }

        if ($response->pagination->getPages() > 1) {
            $response->settings->displayFooter = true;
        }

        return $response;
    }

    /**
     * @throws \DomainException When the baseFatEntity is not an instance of Sugarbean or the table doesn't exist.
     */
    private function generateQueryBuilder(
        ClassMetadata $metadata,
        ListDynamic $list,
        ListBody $params,
        ?string $combinedListKey = null,
        DynamicListResponse $retValue
    ): QueryBuilder {
        if ($list->isExternal() || $list->isCombined()) {
            throw new \InvalidArgumentException(
                "List {$list->getName()} is an external list or a combined one"
            );
        }

        $qb = $this->em->getRepository($metadata->getName())->createQueryBuilder('base');

        $filters = $params->getFilters();
        if (!empty($defaultFilters = $list->getDefaultFilterValues()) && empty($filters)) {
            $filters = $this->filterService->replaceDefaultValues($defaultFilters);
        }

        $this->filterService->addFilterConditions('base', $qb, $list->getFilterFields(), $filters);

        if (!empty($fixedFilter = $list->getStandardFilter())) {
            $fixedFilter = \json_encode($fixedFilter);

            $arguments = $params->getArguments();

            $arguments['current_user_id'] = $this->security->getCurrentUser()->getId();

            $primaryGroup = $this->security->getPrimaryGroup();
            if ($primaryGroup instanceof SecurityGroup) {
                $arguments['current_primary_group_id'] = $primaryGroup->getId();
            }

            foreach ($arguments as $paramKey => $paramValue) {
                if (!\is_array($paramValue)) {
                    $fixedFilter = \str_replace('%' . $paramKey . '%', $paramValue, $fixedFilter);
                }
            }
            $qb->andWhere($fixedFilter);
        }

        if (
            ($params->needsExportToCSV() || $combinedListKey !== null)
            && !empty($params->getRecordIds())
        ) {
            $qb->andWhere($qb->expr()->in('base.id', $params->getRecordIds()));
        }

        if (
            $retValue->settings->quickSearch
            && !empty($quickSearch = $params->getQuickSearch())
        ) {
            $this->filterService->addQuickSearchConditions(
                'base',
                $qb,
                CrudMetadata::getQuickSearchFields($metadata->getName()),
                $quickSearch
            );
        }

        return $qb;
    }

    private function getListIds(
        int $pageSize,
        int $page,
        QueryBuilder $queryBuilder,
        OrderBy $orderBy,
        bool $exportToCSV,
        bool $fixPagination = true
    ): array {
        // @todo re-enable as ACL works for entities
        // $ACLQuery = $this->aclService->getAccessQuery($metadata);
        // if ($ACLQuery !== '') {
        //     $ACLQuery = ' AND ' . $ACLQuery;
        // }

        // get the total amount if needed
        $total = 0;
        if ($exportToCSV || $fixPagination) {
            $total = \count(new Paginator($queryBuilder));
        }

        // fetch the id's we need
        $qb = clone $queryBuilder;
        $qb->select('base.id');
        $qb->orderBy($orderBy);

        if (!$exportToCSV) {
            $qb->setFirstResult(($page - 1) * $pageSize);
            $qb->setMaxResults($pageSize);
        } else {
            if ($total > ListExportService::LIMIT_OF_LIST) {
                return [
                    $total,
                    [],
                ];
            }
        }

        return [
            $total,
            $qb->getQuery()->getArrayResult(),
        ];
    }

    private function fetchRowsForList(
        ?ClassMetadata $metadata,
        int $pageSize,
        ListDynamic $list,
        ListBody $params,
        ?QueryBuilder $queryBuilder,
        DynamicListResponse $response,
        ?string $combinedListKey = null
    ): void {
        $response->pagination->setFixPagination($list->isFixPagination());

        $exportToCSV = $params->needsExportToCSV();

        $sortBy = $params->getSortBy();
        $response->pagination->sortBy = (string) $sortBy;

        if ($externalObject = $list->getExternalObject()) {
            $postedData = [
                'params' => \array_merge($params->getArguments(), $params->getParams(), ['list' => $list]),
                'baseObject' => $list->getBaseObject(),
            ];

            if (!empty($defaultFilters = $list->getDefaultFilterValues())
                && empty($postedData['params']['filters'])
            ) {
                $postedData['params']['filters'] = $defaultFilters;
            }

            if (
                !empty($filters = $list->getStandardFilter())
                && empty($postedData['params']['fixedFilter'])
            ) {
                $postedData['params']['fixedFilter'] = $filters;
            }
            $baseFatEntities = $this->listHandler->getList(
                $externalObject->getClassHandler(),
                $postedData,
                $params->getPage(),
                $exportToCSV ? ListExportService::LIMIT_OF_LIST : $pageSize
            );
            $allBaseFatEntities = $this->filterExternalList($baseFatEntities['list'] ?? [], $params->getRecordIds());

            $response->pagination->total = $baseFatEntities['total'] ?? \count($allBaseFatEntities);
            $response->pagination->pages = $baseFatEntities['totalPages'] ?? $response->pagination->pages;
            $response->postedData = $postedData;
        } else {
            [$count, $allBaseFatEntities] = $this->getListIds(
                $pageSize,
                $params->getPage(),
                $queryBuilder,
                $sortBy,
                $exportToCSV,
                $response->pagination->isFixPagination()
            );

            $response->pagination->total = $count;
        }

        $response->pagination->setCurrentPageSize(\count($allBaseFatEntities));

        $this->fillDynamicList(
            $metadata,
            $exportToCSV,
            $allBaseFatEntities,
            $list,
            $response,
            $sortBy,
            $combinedListKey
        );
    }

    private function fillDynamicList(
        ?ClassMetadata $metadata,
        bool $exportToCSV,
        array $allBaseEntities,
        ListDynamic $list,
        DynamicListResponse $response,
        OrderBy $sortBy,
        ?string $combinedListKey
    ): void {
        $listHelper = $this->parserService;

        $resolverOptions = (new PathResolverOptions)
            ->setExternalLinks(
                ($externalObject = $list->getExternalObject()) ? $externalObject->getLinkFields() : null
            )
            ->setAllBeans($allBaseEntities);

        if (!$list->isExternal()) {
            $allBaseEntities = $this->parserService->parseListQuery(
                $metadata,
                ExpressionGroup::createForCellsAndTopActions(
                    $list->getCellLinks(),
                    $exportToCSV,
                    $response->topBar !== false ? $response->topBar : null
                ),
                (new PathResolverOptions())->setAllBeans($allBaseEntities),
                $sortBy,
                true
            );
        }

        foreach ($allBaseEntities as $baseFatEntityId) {
            if (\is_object($baseFatEntityId)) {
                $baseEntity = $baseFatEntityId;
            } elseif (isset($baseFatEntityId['id'])) {
                $baseEntity = $this->em->getRepository($metadata->getName())->find($baseFatEntityId['id']);
            } else {
                throw new \RuntimeException("This list row object is not an object nor an id array");
            }

            $dynamicListRow = new DynamicListRow();

            if (\method_exists($baseEntity, 'getId') && !empty($baseEntity->getId())) {
                $dynamicListRow->id = $baseEntity->getId();
            } elseif (!empty($baseEntity->id)) {
                $dynamicListRow->id = (string) $baseEntity->id;
            }

            $explodedSort = \explode(' ', (string) $sortBy);

            if (isset($explodedSort[1])) {
                $dynamicListRow->sortByOrder = $explodedSort[1];
            }
            if (isset($baseEntity->{$explodedSort[0]})
                || (
                    $explodedSort[0] === ListSortingOption::DEFAULT_SORT
                    && \method_exists($baseEntity, 'getCreatedAt')
                )
            ) {
                if ($explodedSort[0] === ListSortingOption::DEFAULT_SORT) {
                    if (\method_exists($baseEntity, 'getCreatedAt')) {
                        $dynamicListRow->sortBy = $baseEntity->getCreatedAt();
                    } elseif ($baseEntity->getDateEntered()) {
                        $dynamicListRow->sortBy = $baseEntity
                            ->getDateEntered()
                            ->format(Format::DB_DATETIME_FORMAT)
                        ;
                    }
                } else {
                    $dynamicListRow->sortBy = $baseEntity->{$explodedSort[0]};
                }
            } else {
                $explodedSort = \explode('|', $explodedSort[0]);
            }

            $parserOptions = (new ExpressionParserOptions($baseEntity))
                ->setReplaceEnumValueWithLabel(true);
            $jsonParserOptions = (clone $parserOptions)->setContext(ExpressionParserOptions::CONTEXT_JSON);

            foreach ($list->getCellLinks() as $cellLink) {
                $cell = $cellLink->getCell();
                if (!$cell->isVisible($exportToCSV)) {
                    continue;
                }

                if (!empty($params = $cell->getParams())) {
                    $params = $listHelper->parseListValue(
                        $jsonParserOptions,
                        \json_encode($params),
                        '',
                        $resolverOptions
                    );

                    if (DataCleaner::isJson($params)) {
                        $params = DataCleaner::jsonDecode($params);
                    }

                    if (!empty($params) && isset($params['visible']) && !$params['visible']) {
                        continue;
                    }
                } else {
                    $params = [];
                }

                $dynamicListRowCell = new DynamicListRowCell();
                $dynamicListRowCell->type = $cell->getType();

                //Types are hardcoded
                switch ($cell->getType()) {
                    case CellType::LINK_BOLD_TOP_TWO_LINER:
                    case CellType::LINK_PINK_DOWN_TOP_TWO_LINER:
                    case CellType::SIMPLE_TWO_LINER:
                    case CellType::ICON_LINK:
                        $dynamicListRowCell->class = 'cell__text';
                        $dynamicListRowCell->options->line1 = $listHelper->parseListValue(
                            $parserOptions,
                            $cell->getLine1(),
                            '',
                            $resolverOptions
                        );
                        $dynamicListRowCell->options->line2 = $listHelper->parseListValue(
                            $parserOptions,
                            $cell->getLine2(),
                            '',
                            $resolverOptions
                        );
                        $dynamicListRowCell->options->link = $listHelper->parseListValue(
                            $baseEntity,
                            $cell->getLink(),
                            null,
                            $resolverOptions
                        );

                        if (!empty($sortBy)
                            && empty($dynamicListRow->sortBy)
                            && isset($explodedSort[0], $explodedSort[1])
                            && $explodedSort[0] === $cell->getName()
                        ) {
                            $dynamicListRow->sortBy = $dynamicListRowCell->options->{$explodedSort[1]};
                        }

                        $dynamicListRowCell->options->icon = $listHelper->parseListValue(
                            $baseEntity,
                            $cell->getIcon(),
                            null,
                            $resolverOptions
                        );
                        $dynamicListRowCell->options->linkTo = $cell->getLinkto();

                        $dynamicListRowCell->cellLines->line1 = !empty($cell->getLine1());
                        $dynamicListRowCell->cellLines->line2 = !empty($cell->getLine2());
                        $dynamicListRowCell->cellLines->line3 = !empty($cell->getLine3());
                        break;
                    case CellType::DROPDOWN:
                        $labels = $listHelper->parseListValue(
                            $parserOptions,
                            $cell->getLine2(),
                            '',
                            $resolverOptions
                        );
                        $options = [];
                        if (!\is_array($labels)) {
                            $labels = [];
                        }
                        foreach ($labels as $optionKey => $label) {
                            $option = [
                                'label' => $label,
                            ];
                            if (!empty($cell->getLinkto()) && !empty($params) && isset($params[$optionKey])) {
                                $option['action'] = [
                                    'command' => 'navigate',
                                    'arguments' => [
                                        'linkTo' => \str_replace('_', '-', $cell->getLinkto()),
                                        'params' => DataCleaner::jsonDecode($params[$optionKey], false),
                                    ],
                                ];
                            }
                            $options[] = $option;
                        }

                        if (\count($options) === 0) {
                            $dynamicListRowCell->type = 'list_simple_two_liner_cell';
                            $dynamicListRowCell->class = 'cell__text';
                            $dynamicListRowCell->options->line1 = \str_replace(
                                '%count%',
                                '0',
                                $cell->getLine1()
                            );
                        } elseif (\count($options) === 1) {
                            if (!empty($options[0]['action']['arguments']['params'])) {
                                $dynamicListRowCell->type = 'list_link_bold_top_two_liner_cell';
                                $dynamicListRowCell->options->params =
                                    $options[0]['action']['arguments']['params'] ?? '';
                            } else {
                                $dynamicListRowCell->type = 'list_simple_two_liner_cell';
                            }
                            $dynamicListRowCell->class = 'cell__text';
                            $dynamicListRowCell->options->line1 = $options[0]['label'];
                        } else {
                            $dynamicListRowCell->class = 'cell__select';
                            $dynamicListRowCell->options->id = $dynamicListRow->id;
                            $dynamicListRowCell->options->defaultOption = \str_replace(
                                '%count%',
                                \count($options),
                                $cell->getLine1()
                            );
                            $dynamicListRowCell->options->dropdownOptions = $options;
                        }

                        break;
                    case CellType::PLUS:
                        $dynamicListRowCell->class = 'cell__action';
                        $dynamicListRowCell->options->id = $dynamicListRow->id;
                        $dynamicListRowCell->options->gridKey = $params['grid'] ?? GridService::DEFAULT_ACTION_BAR_GRID;
                        $dynamicListRowCell->options->listKey = $combinedListKey ?? $list->getName();

                        if (!empty($params['icon-close'])) {
                            $dynamicListRowCell->options->iconClose = $params['icon-close'];
                        }

                        if (!empty($params['icon-open'])) {
                            $dynamicListRowCell->options->iconOpen = $params['icon-open'];
                        }

                        break;
                    case CellType::ACTION:
                        $dynamicListRowCell->class = 'cell__action';
                        $dynamicListRowCell->options->action = [];
                        $dynamicListRowCell->options->label = $listHelper->parseListValue(
                            $parserOptions,
                            $cell->getLine1(),
                            '',
                            $resolverOptions
                        );
                        $actionExpression = $cell->getActionKey();
                        $dynamicListRowCell->options->action['id'] =
                            $listHelper->parseListValue(
                                $parserOptions,
                                $cell->getActionKey(),
                                '',
                                $resolverOptions
                            );

                        // Check if the action expression also has a record type and record id
                        $recordTypeExpression = $this->replaceLastExpressionPart(
                            $actionExpression,
                            'action_record_type%'
                        );
                        $recordIdExpression = $this->replaceLastExpressionPart(
                            $actionExpression,
                            'action_record_id%'
                        );

                        $dynamicListRowCell->options->action['recordId'] =
                            $listHelper->parseListValue(
                                $baseEntity,
                                $recordIdExpression,
                                $dynamicListRow->id,
                                $resolverOptions
                            );
                        if (empty($dynamicListRowCell->options->action['recordId'])) {
                            $dynamicListRowCell->options->action['recordId'] = $dynamicListRow->id;
                        }
                        $dynamicListRowCell->options->action['recordType'] =
                            $listHelper->parseListValue(
                                $baseEntity,
                                $recordTypeExpression,
                                $list->getBaseObject(),
                                $resolverOptions
                            );
                        if (empty($dynamicListRowCell->options->action['recordType'])) {
                            $dynamicListRowCell->options->action['recordType'] = $list->getBaseObject();
                        }
                        $dynamicListRowCell->options->action['listKey'] = $list->getName();

                        $dynamicListRowCell->options->icon = $listHelper->parseListValue(
                            $baseEntity,
                            $cell->getIcon(),
                            null,
                            $resolverOptions
                        );

                        $dynamicListRowCell->options->clickable = true;
                        break;
                    case CellType::ICON_TEXT:
                        $dynamicListRowCell->class = 'cell__text';
                        $dynamicListRowCell->options->cssClasses =
                            $listHelper->parseListValue($baseEntity, $cell->getIcon(), '', $resolverOptions);

                        $dynamicListRowCell->options->text = $listHelper->parseListValue(
                            $parserOptions,
                            $cell->getLine1(),
                            '',
                            $resolverOptions
                        );
                        break;
                    case CellType::CHECKBOX: //No  data cell :-)
                        $dynamicListRowCell->class = 'cell__checkbox';
                        $dynamicListRowCell->options->id = $dynamicListRow->id;
                        $dynamicListRowCell->options->listKey = $combinedListKey ?? $list->getName();
                        break;
                }

                if (isset($params['cssClasses'])) {
                    if (\is_array($params['cssClasses'])) {
                        $params['cssClasses'] = \implode(' ', $params['cssClasses']);
                    }

                    $dynamicListRowCell->class = ($dynamicListRowCell->class ?? '') . ' ' . $params['cssClasses'];
                    unset($params['cssClasses']);
                }

                if (empty($dynamicListRowCell->options->params) && $cell->getType() !== CellType::DROPDOWN) {
                    $dynamicListRowCell->options->params = $params;
                }

                $dynamicListRowCell->options->linkTo = \str_replace('_', '-', $cell->getLinkto());

                if (isset($dynamicListRowCell->options->line1)) {
                    $dynamicListRowCell->options->line1 = DataCleaner::cleanOutput($dynamicListRowCell->options->line1);
                }

                if (isset($dynamicListRowCell->options->line2)) {
                    $dynamicListRowCell->options->line2 = DataCleaner::cleanOutput($dynamicListRowCell->options->line2);
                }

                $dynamicListRow->cells[] = $dynamicListRowCell;
            }
            $raw = [];
            if (isset($baseEntity->parsedData)) {
                $raw = $baseEntity->parsedData;
            } elseif ($baseEntity instanceof \JsonSerializable || $baseEntity instanceof \stdClass) {
                $raw = DataCleaner::jsonDecode(\json_encode($baseEntity), false);
            }
            $dynamicListRow->rowData = $raw;
            $response->rows[] = $dynamicListRow;
        }
    }

    public function fillTopBarOnList(
        ListDynamic $sourceList,
        ListBody $listParams,
        DynamicListResponse $response
    ): void {
        if (!($topBar = $sourceList->getTopBar())) {
            $response->topBar = false;

            return;
        }

        $response->topBar->selectAll = $topBar->getSelectAll();
        $response->topBar->canExportToCSV = $topBar->getCanExportToCsv();

        $baseEntity = $sourceList;
        if (!$sourceList->isExternal()
            && !empty($listParams->getRecordType())
            && !empty($listParams->getRecordId())
        ) {
            $baseEntity = $this->em->getRepository($listParams->getRecordType())->find($listParams->getRecordId());
        }

        foreach ($topBar->getActions() as $action) {
            if (($flowAction = $action->getFlowAction())
                && !$this->actionService->isHidden($action, $baseEntity)
            ) {
                $dynamicListTopBarButton = new DynamicListTopBarButton();
                $dynamicListTopBarButton->label = $this->translator->trans(
                    $action->getName(),
                    [],
                    TranslationDomain::LIST_TOPBAR
                );
                $dynamicListTopBarButton->CALLBACK = $action->getType();
                $dynamicListTopBarButton->icon = $action->getIcon();
                $dynamicListTopBarButton->action = ['id' => $flowAction->getGuid()];
                $dynamicListTopBarButton->enabled = $this->actionService->isEnabled($action, $baseEntity);

                if (!empty($params = $action->getParams())) {
                    $params = $this->parserService->parseListValue(
                        (new ExpressionParserOptions(new Model($listParams->getArguments())))
                            ->setContext(ExpressionParserOptions::CONTEXT_JSON),
                        \json_encode($params)
                    );
                    $params = DataCleaner::jsonDecode($params);

                    if (isset($params['recordId'])) {
                        $params['recordId'] = $this->parserService->parseListValue(
                            $baseEntity,
                            $params['recordId'],
                            $params['recordId']
                        );
                    }
                    $dynamicListTopBarButton->action = \array_merge($dynamicListTopBarButton->action, $params);
                }

                if (isset($dynamicListTopBarButton->action['mandatorySelectRecordMessage'])) {
                    $dynamicListTopBarButton->action['mandatorySelectRecordMessage'] = $this->translator->trans(
                        $dynamicListTopBarButton->action['mandatorySelectRecordMessage'],
                        [],
                        TranslationDomain::LIST_TOPBAR
                    );
                }

                $response->topBar->buttons[] = $dynamicListTopBarButton;
            }
        }

        foreach ($topBar->getSortingOptions() as $sorting) {
            $topBarSorting = new DynamicListTopBarSorting();
            $topBarSorting->label = $this->translator->trans(
                $sorting->getName(),
                [],
                TranslationDomain::LIST_TOPBAR
            );
            $topBarSorting->key = $sorting->getId();
            $response->topBar->sortingOptions[] = $topBarSorting;
        }
    }

    private function getHeadingsForList(ListDynamic $list, bool $exportToCSV): array
    {
        $headers = [];

        foreach ($list->getCellLinks() as $cellLink) {
            $cell = $cellLink->getCell();
            if ($cell->isVisible($exportToCSV)) {
                $header = new DynamicListHeader();
                $header->cellType = $cell->getType();
                $header->label = $this->translator->trans($cell->getColumnLabel(), [], TranslationDomain::LIST_COLUMN);

                $header->cellLines->line1 = !empty($cell->getLine1());
                $header->cellLines->line1CsvHeader =
                    empty($cell->getLine1CsvHeader()) ?
                        $this->translator->trans($cell->getColumnLabel(), [], TranslationDomain::LIST_COLUMN) :
                        $this->translator->trans($cell->getLine1CsvHeader(), [], TranslationDomain::LIST_COLUMN);

                $header->cellLines->line2 = !empty($cell->getLine2());
                $header->cellLines->line2CsvHeader =
                    empty($cell->getLine2CsvHeader()) ?
                        $this->translator->trans($cell->getColumnLabel(), [], TranslationDomain::LIST_COLUMN) :
                        $this->translator->trans($cell->getLine2CsvHeader(), [], TranslationDomain::LIST_COLUMN);

                $header->cellLines->line3 = !empty($cell->getLine3());
                $header->cellLines->line3CsvHeader =
                    empty($cell->getLine3CsvHeader()) ?
                        $this->translator->trans($cell->getColumnLabel(), [], TranslationDomain::LIST_COLUMN) :
                        $this->translator->trans($cell->getLine3CsvHeader(), [], TranslationDomain::LIST_COLUMN);
                $headers[] = $header;
            }
        }

        return $headers;
    }

    private function filterExternalList(array $objects, array $recordIds): array
    {
        //IF SOME RECORD ID'S WERE SELECTED, ONLY RETURN THOSE IN EXTERNAL LISTS
        if (!\count($objects) || !\count($recordIds)) {
            return $objects;
        }

        return \array_filter(
            $objects,
            function ($object) use ($recordIds) {
                return \in_array($object->getId(), $recordIds, true);
            }
        );
    }

    private function replaceLastExpressionPart(string $expression, string $replacementPart): string
    {
        $expressionParts = \explode('|', $expression);
        \array_pop($expressionParts);
        $expressionParts[] = $replacementPart;
        $expressionString = \implode('|', $expressionParts);

        if (1 === \count($expressionParts)) {
            return '%' . $expressionString;
        }

        return $expressionString;
    }

    private function getCombinedList(
        ListDynamic $list,
        DynamicListResponse $response,
        ListBody $params,
        int $pageSize = -1
    ): void {
        $response->topBar = null;
        $currentPage = $params->getPage() ?? 1;

        foreach ($list->getExternalObject()->getLinkFields() as $externalObjectLink) {
            $listParams = clone $params;
            $listParams->configure(
                $params->getArguments(),
                [
                    'page' => 1,
                ]
            );

            $result = $this->getList(
                $externalObjectLink->getList(),
                $listParams,
                $list->getId()
            );

            if (empty($response->headers)) {
                $response->headers = $result->headers;
            }
            if ($response->topBar === null) {
                $response->topBar = $result->topBar;
            }
            $response->rows = \array_merge($response->rows, $result->rows);
        }

        \usort(
            $response->rows,
            function ($a, $b) {
                if (\property_exists($a, 'sortBy') && \property_exists($b, 'sortBy')) {
                    if (\strtolower($a->sortByOrder) === 'desc') {
                        return $b->sortBy <=> $a->sortBy;
                    }

                    return $a->sortBy <=> $b->sortBy;
                }

                return 0;
            }
        );

        $this->paginateCombinedRows(
            $response,
            $currentPage,
            ($params->needsExportToCSV() === true) ? \count($response->rows) : $pageSize
        );
    }

    private function paginateCombinedRows(DynamicListResponse $response, int $page, int $limit): void
    {
        $response->pagination->total = \count($response->rows);
        if ($limit === -1) {
            $response->pagination->pages = 1;
            $response->pagination->size = $response->pagination->total;
        } else {
            $response->pagination->pages = (int) \ceil($response->pagination->total / $limit);
            $response->pagination->size = $limit;

            $bottomLimit = ($page - 1) * $limit;
            $response->rows = \array_slice($response->rows, $bottomLimit, $limit);
        }
    }
}
