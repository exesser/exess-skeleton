<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Doctrine\Type\CellType;
use ExEss\Bundle\CmsBundle\Doctrine\Type\TranslationDomain;
use ExEss\Bundle\CmsBundle\Entity\ListDynamic;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses\DynamicListRowBarButton;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;
use ExEss\Bundle\CmsBundle\Servicemix\ExternalObjectHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListRowBarService
{
    private ExternalObjectHandler $externalObjectHandler;

    private ActionService $actionService;

    private TranslatorInterface $translator;

    private ParserService $parserService;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        ExternalObjectHandler $externalObjectHandler,
        ActionService $actionService,
        TranslatorInterface $translator,
        ParserService $parserService
    ) {
        $this->externalObjectHandler = $externalObjectHandler;
        $this->actionService = $actionService;
        $this->translator = $translator;
        $this->parserService = $parserService;
        $this->em = $em;
    }

    public function findListRowActions(ListDynamic $list, string $recordId, array $actionData): array
    {
        $externalObject = $list->getExternalObject();
        $baseObject = null;

        if ($externalObject && $list->isCombined()) {
            // loop over all combined lists
            foreach ($externalObject->getLinkFields() as $link) {
                $linkedList = $link->getList();

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
                        'listKey' => $listName = $list->getName(),
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
                            $actionParams['recordId'] = $this->parserService->parseListValue(
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
