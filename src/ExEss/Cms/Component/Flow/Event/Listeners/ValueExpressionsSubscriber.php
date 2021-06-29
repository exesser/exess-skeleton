<?php

namespace ExEss\Cms\Component\Flow\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\ParserService;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Component\Flow\Event\FlowEvent;
use ExEss\Cms\Component\Flow\Event\FlowEvents;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\MultiLevelTemplate\TextFunctionHandler;
use ExEss\Cms\Service\SelectWithSearchService;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ValueExpressionsSubscriber implements EventSubscriberInterface
{
    private TextFunctionHandler $textFunctionHandler;

    private FlashMessageContainer $flashMessageContainer;

    private ParserService $parserService;

    private SelectWithSearchService $selectWithSearchService;

    public function __construct(
        TextFunctionHandler $textFunctionHandler,
        FlashMessageContainer $flashMessageContainer,
        ParserService $parserService,
        SelectWithSearchService $selectWithSearchService
    ) {
        $this->textFunctionHandler = $textFunctionHandler;
        $this->flashMessageContainer = $flashMessageContainer;
        $this->parserService = $parserService;
        $this->selectWithSearchService = $selectWithSearchService;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        // after SuggestionsSubscriber!
        return [
            FlowEvents::INIT => [
                ['resolveExpressions', -75],
            ],
            FlowEvents::NEXT_STEP => [
                ['resolveExpressions', -75],
            ],
            FlowEvents::NEXT_STEP_FORCED => [
                ['resolveExpressions', -75],
            ],
        ];
    }

    /**
     * @throws \LogicException If FormAndModelSubscriber was not run yet.
     */
    public function resolveExpressions(FlowEvent $event): void
    {
        if (!$event->getResponse()->getForm()) {
            throw new \LogicException('ValueExpressionsSubscriber must run after FormAndModelSubscriber');
        }

        $modelReplacements = [];
        foreach ($event->getResponse()->getForm()->getGroups() as $name => $group) {
            if (!\property_exists($group, 'fields')) {
                continue;
            }

            foreach ($group->fields as $key => $field) {
                if (empty($field->valueExpression)) {
                    continue;
                }

                $formulaString = $field->valueExpression;

                $formulaString = $this->replaceField(
                    $formulaString,
                    $event->getModel(),
                    $event->getBaseEntity()
                );

                if (\is_array($formulaString)) {
                    $formulaString = \implode(', ', $formulaString);
                }

                try {
                    $modelReplacements[$field->id] = \is_bool($formulaString)
                        ? $formulaString
                        : $this->textFunctionHandler->resolveFunctions($formulaString);

                    if (isset($field->type, $field->datasourceName)
                        && $field->type === FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH
                    ) {
                        $modelReplacements[$field->id] = $this->selectWithSearchService->getLabelsForValues(
                            $field->datasourceName,
                            [$modelReplacements[$field->id]]
                        );
                    }
                } catch (InvalidArgumentException $e) {
                    $modelReplacements[$field->id] = '**error**';
                    $this->flashMessageContainer->addFlashMessage(
                        new FlashMessage($field->id . ': Formula error: ' . $e->getMessage())
                    );
                }
            }
        }

        // ModelReplacements will now only be done at the end of the functions. This is because otherwise the order
        // will affect the results of the formula’s as the output of the previous will affect the input of the next.
        // Now all replacements are stashed and when all calculations are done basis the original model values,
        // we will replace the values in the model
        foreach ($modelReplacements as $modelFieldId => $modelReplacement) {
            if (
                empty($modelReplacement)
                && !\is_numeric($modelReplacement)
                && \is_null($event->getModel()->getFieldValue($modelFieldId, '', true))
            ) {
                continue;
            }
            $event->getModel()->$modelFieldId = $modelReplacement;
        }
    }

    /**
     * @throws InvalidArgumentException When the key is not found in the model.
     * @return string|bool
     */
    private function replaceField(
        string $formulaString,
        Model $model,
        ?object $baseEntity = null
    ) {
        \preg_match_all('/{iterator}(.*?){\/iterator}/s', $formulaString, $iteratorMatches);
        if (\count($iteratorMatches[1]) !== 0) {
            [$modelKey, $value] = \explode(';', $iteratorMatches[1][0], 2);

            if (!$model->offsetExists($modelKey)) {
                throw new InvalidArgumentException(\sprintf('The key "%s" doesn\'t exist in the model', $modelKey));
            }

            $iteratorList = $model->getFieldValue($modelKey);
            /** @see ValueExpressionsTest - For all the supported formats */
            if (\is_string($iteratorList)) {
                $iteratorList = new Model([$iteratorList]);
            }

            if ($iteratorList instanceof Model) {
                $foundItems = $this->replaceIteratorModelStructure($iteratorList);
            }
        }

        \preg_match_all('/\%([^%]*)\%/', $formulaString, $matches);

        // We are dealing with a iterator field, we need a different output.
        if (isset($value, $foundItems)) {
            if (\count($matches[1]) === 0) {
                return '';
            }
            return $this->iteratorFieldOutput($foundItems, $matches[1], $value);
        }

        foreach ($matches[1] as $match) {
            if (
                $model->hasField($match, true)
                && !empty($fieldValue = $model->getFieldValue($match, '', true))
            ) {
                $formulaString = \str_replace(
                    '%' . $match . '%',
                    $fieldValue,
                    $formulaString
                );
            }
        }

        return $this->parserService->parseListValue(
            $baseEntity ?? $model,
            $formulaString
        );
    }

    private function replaceIteratorModelStructure(
        Model $iteratorList,
        string $parentKey = '',
        array $foundItems = []
    ): array {

        foreach ($iteratorList as $key => $item) {
            if (!\is_string($item) && $item->current() instanceof Model) {
                $foundItems = $this->replaceIteratorModelStructure($item, $key, $foundItems);
            }

            if (\is_string($item)) {
                $item = new Model(['id' => $item]);
                $foundItems[] = $item;
            }

            if (!isset($item->id)) {
                if (!empty($parentKey)) {
                    $item->id = $parentKey;
                    $foundItems[] = $item;
                } else {
                    $item->id = $key;
                    if (!$item[0] instanceof Model) {
                        $foundItems[] = $item;
                    }
                }
            }
        }

        return $foundItems;
    }

    private function iteratorFieldOutput(array $foundItems, array $matches, string $value): string
    {
        $totalValue = '';
        $originalValue = $value;
        foreach ($foundItems as $item) {
            $value = $originalValue;
            $value = $this->parserService->parseListValue($item, $value);
            $totalValue .= $value;
        }

        return $totalValue;
    }
}
