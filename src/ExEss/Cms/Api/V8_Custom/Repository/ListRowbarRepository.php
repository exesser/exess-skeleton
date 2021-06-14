<?php
namespace ExEss\Cms\Api\V8_Custom\Repository;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Api\V8_Custom\Service\DataCleaner;
use ExEss\Cms\Doctrine\Type\CellType;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\Entity\ListDynamic;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListRowBarButton;
use ExEss\Cms\ListFunctions\HelperClasses\ListHelperFunctions;
use ExEss\Cms\Service\ActionService;
use ExEss\Cms\Servicemix\ExternalObjectHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListRowbarRepository
{
    private ExternalObjectHandler $externalObjectHandler;

    private ActionService $actionService;

    private TranslatorInterface $translator;

    private ListHelperFunctions $listHelper;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        ExternalObjectHandler $externalObjectHandler,
        ActionService $actionService,
        TranslatorInterface $translator,
        ListHelperFunctions $listHelper
    ) {
        $this->externalObjectHandler = $externalObjectHandler;
        $this->actionService = $actionService;
        $this->translator = $translator;
        $this->listHelper = $listHelper;
        $this->em = $em;
    }

    public function findListRowActions(string $listName, string $recordId, array $actionData): array
    {
        /** @var ListDynamic $list */
        $list = $this->em->getRepository(ListDynamic::class)->get($listName);

        $externalObject = $list->getExternalObject();
        $baseObject = null;

        if ($externalObject && $list->isCombined()) {
            // loop over all combined lists
            foreach ($externalObject->getLinkFields() as $link) {
                /** @var ListDynamic $linkedList */
                $linkedList = $this->em->getRepository(ListDynamic::class)->get($link->getName());

                if ($linkedExternalObject = $linkedList->getExternalObject()) {
                    $baseObject = $this->externalObjectHandler->getObject(
                        $linkedExternalObject->getClassHandler(),
                        ['recordId' => $recordId]
                    );
                } else {
                    $baseObject = $this->em->getRepository($linkedList->getBaseObject())->find($recordId);
                }

                // check if a fat entity for the base object of the list and the given recordId exists
                // If it does, we need this list
                if ($baseObject !== null) {
                    break;
                }
            }
        } elseif ($externalObject && !$externalObject->isListOnly()) {
            $baseObject = $this->externalObjectHandler->getObject(
                $externalObject->getClassHandler(),
                ['recordId' => $recordId, 'actionData' => $actionData]
            );
        } else {
            $baseObject = $this->em->getRepository($list->getBaseObject())->find($recordId);
        }

        if ($baseObject === null) {
            throw new NotFoundException("Base object with id $recordId not found!");
        }

        $buttons = [];
        foreach ($list->getCellLinks() as $linkCell) {
            if ($linkCell->getCell()->getType() !== CellType::PLUS) {
                continue;
            }

            foreach ($linkCell->getRowBar()->getRowActions() as $action) {
                $flowAction = $action->getFlowAction();

                $button = new DynamicListRowBarButton();
                $button->label = $this->translator->trans($action->getActionName(), [], TranslationDomain::LIST_ROWBAR);
                $button->icon = $action->getIcon();

                if ($baseObject !== null) {
                    $button->enabled = $this->actionService->isEnabled($action, $baseObject);
                    if ($this->actionService->isHidden($action, $baseObject)) {
                        continue;
                    }
                }

                if ($button->enabled) {
                    $button->action = [
                        'id' => $flowAction ? $flowAction->getGuid() : '',
                        'recordId' => $recordId,
                        'listKey' => $list->getName(),
                        'recordType' => $list->getBaseObject() ?? ($baseObject ? \get_class($baseObject) : null),
                        'actionData' => !empty($actionData) ? $actionData : null,
                    ];

                    if (!empty($actionParams = $action->getParams())) {
                        $actionParams = DataCleaner::replaceParamValuesAndDecodeJson(
                            $actionParams,
                            [
                                'recordId' => $recordId,
                                'listKey' => $listName,
                            ]
                        );

                        if (isset($actionParams['recordId'])) {
                            $actionParams['recordId'] = $this->listHelper->parseListValue(
                                $baseObject,
                                $actionParams['recordId'],
                                $actionParams['recordId']
                            );
                        }
                        $button->action = \array_merge($button->action, $actionParams);
                    }
                }

                $buttons[] = $button;
            }
        }

        return $buttons;
    }
}
