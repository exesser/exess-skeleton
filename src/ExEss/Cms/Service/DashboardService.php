<?php declare(strict_types=1);

namespace ExEss\Cms\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\CRUD\Config\CrudMetadata;
use ExEss\Cms\DASH_Dashboard\DashboardCalcFunctions;
use ExEss\Cms\Doctrine\Type\DashboardType;
use ExEss\Cms\Doctrine\Type\GridType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\DashboardMenuAction;
use ExEss\Cms\Entity\DashboardMenuActionGroup;
use ExEss\Cms\Entity\GridPanel;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Validator;
use ExEss\Cms\Component\ExpressionParser\ParserService;
use ExEss\Cms\MultiLevelTemplate\TextFunctionHandler;
use ExEss\Cms\Component\ExpressionParser\Parser\ExpressionParserOptions;
use ExEss\Cms\Servicemix\ExternalObjectHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardService
{
    private const PANEL_KEY = 'panelKey';
    private const PANEL_TYPE_LIST = 'list';

    private GridService $gridService;
    private TranslatorInterface $translator;
    private ActionService $actionService;
    private ExternalObjectHandler $externalObjectHandler;
    private DashboardCalcFunctions $dashboardCalcFunctions;
    private ParserService $parserService;
    private Validator $validator;
    private TextFunctionHandler $textFunctionHandler;
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        GridService $gridService,
        TranslatorInterface $translator,
        ActionService $actionService,
        DashboardCalcFunctions $dashboardCalcFunctions,
        ParserService $parserService,
        Validator $validator,
        TextFunctionHandler $textFunctionHandler,
        Security $security,
        ExternalObjectHandler $externalObjectHandler
    ) {
        $this->gridService = $gridService;
        $this->translator = $translator;
        $this->actionService = $actionService;
        $this->externalObjectHandler = $externalObjectHandler;
        $this->dashboardCalcFunctions = $dashboardCalcFunctions;
        $this->parserService = $parserService;
        $this->validator = $validator;
        $this->textFunctionHandler = $textFunctionHandler;
        $this->security = $security;
        $this->em = $em;
    }

    public function getDashboard(Dashboard $dashboard, array $arguments = [], ?string $recordId = null): array
    {
        $arguments['recordType'] = $arguments['recordType'] ?? $dashboard->getMainRecordType();
        $arguments['listKey'] = $arguments['listKey'] ?? '';
        $arguments['recordId'] = $recordId;
        $arguments = $this->gridService->getAllArguments($arguments);

        $baseEntity = [];
        if ($baseObject = $this->getBaseObject($dashboard, $arguments)) {
            $entityName = \get_class($baseObject);
            if ($this->em->getMetadataFactory()->hasMetadataFor($entityName)) {
                $baseEntity = [
                    'baseEntity' => \sprintf(
                        "[ %s ] - %s",
                        $this->translator->trans($entityName, [], TranslationDomain::MODULE),
                        CrudMetadata::getCrudListC1R1(
                            $this->em->getMetadataFactory()->getMetadataFor($entityName),
                            $baseObject
                        ),
                    )
                ];
            }
        }

        $baseObject = $baseObject ?? $dashboard;
        $grid = $this->getGridConfig($dashboard, $arguments, $baseObject);

        return \array_merge(
            [
                'title' => $dashboard->getName(),
                'search' => $this->getSearchConfig($dashboard),
                'plusMenu' => [
                    'display' => $dashboard->getDashboardMenu() !== null,
                    'buttons' => $this->getButtonConfig($dashboard, $arguments, $baseObject),
                ],
                'filters' => $this->getFiltersConfig($dashboard, $grid),
                'grid' => $grid
            ],
            $baseEntity
        );
    }

    private function getFiltersConfig(Dashboard $dashboard, array $grid): array
    {
        $config = ['display' => false];

        if ($filter = $dashboard->getFilter()) {
            $config['display'] = true;
            $config['filterKey'] = $filter->getKey();
            if (!empty($listKey = $dashboard->getFiltersListkey())) {
                $config['listKey'] = $listKey;
            } elseif (isset($grid['columns'][0]['rows'][0]['options']['listKey'])) {
                $config['listKey'] = $grid['columns'][0]['rows'][0]['options']['listKey'];
            }
        }

        return $config;
    }

    private function getSearchConfig(Dashboard $dashboard): array
    {
        $config = ['display' => false];

        if ($search = $dashboard->getSearch()) {
            $config['display'] = true;
            $config['linkTo'] = \str_replace('_', '-', $search->getLinkTo());
            if (!empty($params = $search->getParams())) {
                $config['params'] = $params;
            }
        }

        return $config;
    }

    /**
     * @throws \LogicException More than one dashboard found.
     */
    private function getButtonConfig(Dashboard $dashboard, array $arguments, object $baseObject): array
    {
        $buttons = [];
        if (empty($dashboardMenu = $dashboard->getDashboardMenu())) {
            return $buttons;
        }

        $external = $dashboard->getType() === DashboardType::EXTERNAL;

        foreach ($dashboardMenu->getActions() as $menuAction) {
            if (!$this->actionService->isHidden($menuAction, $baseObject)) {
                $buttons[] = $this->getActionButton($external, $menuAction, $arguments, $baseObject);
            }
        }

        foreach ($dashboardMenu->getGroups() as $actionGroup) {
            if (!$this->actionService->isHidden($actionGroup, $baseObject)) {
                $buttons[] = $this->getActionGroupButton($external, $actionGroup, $baseObject, $arguments);
            }
        }

        \usort($buttons, [$this, 'sortByOrder']);

        return $buttons;
    }

    /**
     * @param object|\ExEss\Cms\Base\Response\BaseResponse $fatEntity
     */
    private function getActionGroupButton(
        bool $external,
        DashboardMenuActionGroup $actionGroup,
        object $fatEntity,
        array $arguments
    ): array {
        $buttons = [];

        foreach ($actionGroup->getChildren() as $subActionGroup) {
            if (!$this->actionService->isHidden($subActionGroup, $fatEntity)) {
                $buttons[] = $this->getActionGroupButton($external, $subActionGroup, $fatEntity, $arguments);
            }
        }

        foreach ($actionGroup->getActions() as $menuAction) {
            if (!$this->actionService->isHidden($menuAction, $fatEntity)) {
                $buttons[] = $this->getActionButton($external, $menuAction, $arguments, $fatEntity);
            }
        }

        \usort($buttons, [$this, 'sortByOrder']);

        return [
            'label' => $this->translator->trans($actionGroup->getLabel(), [], TranslationDomain::PLUS_MENU),
            'icon' => $actionGroup->getIcon(),
            'class' => $actionGroup->getClass(),
            'sort_order' => $actionGroup->getSortOrder(),
            'buttonGroup' => true,
            'buttons' => $buttons,
        ];
    }

    protected function sortByOrder(array $a, array $b): int
    {
        return $a['sort_order'] <=> $b['sort_order'];
    }

    private function getActionButton(
        bool $external,
        DashboardMenuAction $menuAction,
        array $arguments,
        object $fatEntity
    ): array {
        $flowAction = $menuAction->getFlowAction();

        if (empty($flowAction->getGuid())) {
            throw new \LogicException('linked action fat entity is not complete');
        }

        $button = [
            'enabled' => $this->actionService->isEnabled($menuAction, $fatEntity),
            'label' => $this->translator->trans($menuAction->getLabel(), [], TranslationDomain::PLUS_MENU),
            'icon' => $menuAction->getIcon(),
            'sort_order' => $menuAction->getSortOrder(),
            'buttonGroup' => false,
            'action' => [
                'id' => $flowAction->getGuid(),
            ],
        ];

        if (!empty($params = $menuAction->getParams())) {
            $parserOptions = (new ExpressionParserOptions(new Model($arguments)))
                ->setContext(ExpressionParserOptions::CONTEXT_JSON);
            $params = \json_decode(
                $this->parserService->parseListValue($parserOptions, \json_encode($params)),
                true
            );

            /* check if the recordType of the dashboard is the same
             * with the recordType of the action, otherwise we will replace
             * the recordId with the correct one
             */
            $this->checkRecordTypes($params, $arguments, $external, $fatEntity);

            $button['action'] = \array_merge($button['action'], $params);
        }

        return $button;
    }

    /**
     * @param object|\ExEss\Cms\Base\Response\BaseResponse|Dashboard $baseEntity
     * @throws \LogicException Arguments not available.
     */
    private function checkRecordTypes(
        array &$params,
        array $arguments,
        bool $external,
        object $baseEntity
    ): void {
        if (empty($params['recordId'])
            || empty($arguments['recordId'])
            || $params['recordId'] !== $arguments['recordId']
        ) {
            return;
        }

        if (empty($params['recordType'])) {
            throw new \LogicException('Please add a recordType on the dashboard menu action');
        }

        if (empty($arguments['recordType'])) {
            throw new \LogicException('Please add a `Main record type` on this dashboard:' . $arguments['dash_name']);
        }

        if ($params['recordType'] === $arguments['recordType']) {
            return;
        }

        if ($external) {
            // @todo do something
            return;
        }

        if (!$this->em->getMetadataFactory()->hasMetadataFor(\get_class($baseEntity))) {
            throw new \LogicException(
                'No fat entity found for: ' . $arguments['recordType'] . ':' . $arguments['recordId']
            );
        }
        $metadata = $this->em->getClassMetadata(\get_class($baseEntity));
        $associations = $metadata->getAssociationsByTargetClass($params['recordType']);
        if (empty($associations) || \count($associations) > 1) {
            throw new \LogicException("Multiple or not relations found towards {$params['recordType']}");
        }

        $getter = "get" . \ucfirst($associations[0]);
        $linkedEntity = $baseEntity->$getter();

        if (!$linkedEntity) {
            throw new \LogicException(
                'No record found in `' . $params['recordType'] . '` that is connected with `'
                . $arguments['recordType'] . ':' . $arguments['recordId'] . '`'
            );
        }

        $params['recordId'] = $linkedEntity->getId();
    }

    private function getGridConfig(Dashboard $dashboard, array $arguments, object $baseEntity): array
    {
        $grid = $dashboard->getGridTemplate();

        if (!$grid || empty($jsonFields = $grid->getJsonFields())) {
            return [];
        }

        $gridJson = $this->gridService->encodeJson(\json_encode($jsonFields));
        $gridJson = $this->replaceDashboardProperties($gridJson, $dashboard, $arguments, $baseEntity);
        $gridJson = $this->gridService->replaceArguments($gridJson, $arguments);

        $decodedGrid = \json_decode($gridJson, true, 512, \JSON_THROW_ON_ERROR);
        if (\is_array($decodedGrid)) {
            try {
                $decodedGrid = $this->replacePanelKey($decodedGrid, $dashboard, $arguments, $baseEntity);
            } catch (\UnexpectedValueException $e) {
                unset($decodedGrid[self::PANEL_KEY]);
            }

            $decodedGrid = $this->removeUnauthorizedBlocks($decodedGrid);
        }

        if (\is_array($decodedGrid)) {
            $decodedGrid = $this->translateGrid($decodedGrid);
        }

        return $decodedGrid;
    }

    /**
     * @param null|object|\ExEss\Cms\Base\Response\BaseResponse $baseEntity
     */
    private function replacePanelKey(
        array $grid,
        Dashboard $dashboard,
        array $arguments,
        ?object $baseEntity
    ): array {
        foreach ($grid as $key => $value) {
            if ($key === self::PANEL_KEY) {
                $grid = $this->createPanel($value, $dashboard, $arguments, $baseEntity);
            } elseif (\is_array($value)) {
                try {
                    $grid[$key] = $this->replacePanelKey($value, $dashboard, $arguments, $baseEntity);
                } catch (\UnexpectedValueException $e) {
                    unset($grid[$key]);
                }
            }
        }

        return $grid;
    }

    private function createPanel(
        string $key,
        Dashboard $dashboard,
        array $arguments,
        ?object $baseFatEntity
    ): array {
        /** @var GridPanel $gridPanel */
        $gridPanel = $this->em->getRepository(GridPanel::class)->get($key);

        if (\is_null($baseFatEntity)) {
            $baseFatEntity = $gridPanel;
        }

        if ($gridPanel->getConditions()->count()) {
            $violations = $this->validator->runValidationRules($gridPanel->getConditions(), $baseFatEntity);
        }

        if (!empty($violations)) {
            throw new \UnexpectedValueException('Violations error for grid panel with id: ' . $gridPanel->getId());
        }

        switch ($gridPanel->getType()) {
            case GridType::EMBEDDED_GUIDANCE:
                $panel = [
                    'size' => $gridPanel->getSize(),
                    'type' => $gridPanel->getType(),
                    'cssClasses' => ['card'],
                    'options' => [
                        'recordType' => $gridPanel->getRecordType(),
                        'flowAction' => $gridPanel->getFlowAction(),
                        'flowId' => $gridPanel->getFlowId(),
                        'recordId' => $gridPanel->getRecordId(),
                        'showPrimaryButton' => $gridPanel->getShowPrimaryButton(),
                        'primaryButtonTitle' => $gridPanel->getPrimaryButtonTitle(),
                        'defaultTitle' => $gridPanel->getDefaultTitle() ?? '',
                        'titleExpression' => $gridPanel->getTitleExpression() ?? ''
                    ],
                ];
                if (!empty($params = $gridPanel->getParams())) {
                    $panel['options'] += $params;
                }
                break;
            case GridType::LIST:
                $panel = [
                    'size' => $gridPanel->getSize(),
                    'type' => $gridPanel->getType(),
                    'options' => [
                        'listKey' => ($list = $gridPanel->getList()) ? $list->getName() : null,
                        'params' => !empty($params = $gridPanel->getParams()) ? $params : [],
                    ],
                ];
                break;
            default:
                throw new \UnexpectedValueException('Invalid grid panel type ' . $gridPanel->getType());
        }

        $panel = \json_encode($panel);
        $panel = $this->gridService->replaceArguments($panel, $arguments);
        $panel = $this->replaceDashboardProperties($panel, $dashboard, $arguments, $baseFatEntity);
        $panel = \json_decode($panel, true);

        if (empty($panel['options']['recordId']) && empty($panel['options']['params']['recordId'])) {
            throw new \UnexpectedValueException("The defined grid panel $key has no recordId.");
        }

        foreach (GridService::TO_TRANSLATE_OPTIONS as $toTranslateOption) {
            if (isset($panel['options'][$toTranslateOption])) {
                $panel['options'][$toTranslateOption] = $this->translator->trans(
                    $panel['options'][$toTranslateOption],
                    [],
                    TranslationDomain::DASHBOARD_GRID
                );
            }
        }

        return $panel;
    }

    /**
     * @param null|\ExEss\Cms\Base\Response\BaseResponse|object $baseEntity
     */
    private function replaceDashboardProperties(
        string $json,
        Dashboard $dashboard,
        array $arguments,
        ?object $baseEntity = null
    ): string {
        foreach ($dashboard->getProperties() as $property) {
            $value = $property->getValue();
            if (empty($value)) {
                $replaceVar = $value;
            } elseif (\strpos($property->getName(), 'calc:') !== false && !empty($arguments['recordId'])) {
                $replaceVar = $this->dashboardCalcFunctions->handleProperty($property, $arguments['recordId']);
            } elseif ($value === 'recordId' && !empty($arguments['recordId'])) {
                $replaceVar = $arguments['recordId'];
            } elseif (\preg_match('/\%([^%.]*)\%/', $value) !== false) {
                $replaceVar = $this->parserService->parseListValue($baseEntity, $value, null);
            } else {
                $replaceVar = $value;
            }

            $propertyName = '%' . $property->getName() . '%';
            if (!empty($replaceVar)
                && \in_array(\gettype(\json_decode($replaceVar)), ['array', 'object'], true)
            ) {
                $propertyName = '"' . $propertyName . '"';
            }

            $json = \str_replace($propertyName, $replaceVar, $json);
        }

        return $json;
    }

    private function removeUnauthorizedBlocks(array $grid): array
    {
        // @todo use entities when ACL is active for entities
        foreach ($grid as $key => $value) {
            $blockType = $value['type'] ?? null;

            if ($blockType === self::PANEL_TYPE_LIST) {
                $list = $value['options']['listKey'] ?? null;
                try {
                    $this->em->getRepository(ListDynamic::class)->get($list);
                } catch (\Exception $e) {
                    $grid[$key]['type'] = 'unauthorized-list';
                }
            } elseif (\is_array($value)) {
                $grid[$key] = $this->removeUnauthorizedBlocks($value);
            }
        }

        return $grid;
    }

    /**
     * @return null|object|\ExEss\Cms\Base\Response\BaseResponse
     */
    private function getBaseObject(Dashboard $dashboard, array $arguments): ?object
    {
        $baseEntity = null;
        $baseEntityName = $dashboard->getMainRecordType() ?? $arguments['recordType'] ?? null;

        if (!empty($baseEntityName)) {
            if ($dashboard->getType() === DashboardType::DEFAULT
                && \is_string($recordId = $arguments['recordId'] ?? null)
            ) {
                $baseEntity = $this->em->getRepository($baseEntityName)->find($recordId);
            } elseif ($dashboard->getType() === DashboardType::EXTERNAL) {
                $baseEntity = $this->externalObjectHandler->getObject($baseEntityName, $arguments);
            }
        }

        return $baseEntity;
    }

    private function translateGrid(array $grid): array
    {
        foreach ($grid as $key => $childGrid) {
            if (\is_array($childGrid)) {
                $grid[$key] = $this->translateGrid($childGrid);
            } elseif (\is_string($childGrid)) {
                $grid[$key] = $this->textFunctionHandler->resolveFunctions(
                    $childGrid,
                    $this->security->getPreferredLocale()
                );
            }
        }

        return $grid;
    }
}
