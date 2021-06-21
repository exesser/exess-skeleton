<?php
namespace ExEss\Cms\FLW_Flows;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Api\V8_Custom\Service\SimpleActionFactory;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Doctrine\Type\TranslationDomain;
use ExEss\Cms\FLW_Flows\Action\Arguments;
use ExEss\Cms\FLW_Flows\Event\FlowEventDispatcher;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Service\GridService;
use ExEss\Cms\Users\Service\GuidanceRecoveryService;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionFactory extends SimpleActionFactory
{
    private FlowEventDispatcher $flowEventDispatcher;

    private TranslatorInterface $translator;

    public function __construct(
        EntityManager $em,
        FlowEventDispatcher $flowEventDispatcher,
        Logger $logger,
        GuidanceRecoveryService $guidanceRecoveryService,
        TranslatorInterface $translator
    ) {
        parent::__construct($em, $logger, $guidanceRecoveryService);
        $this->flowEventDispatcher = $flowEventDispatcher;
        $this->translator = $translator;
    }

    protected function handleJson(\stdClass $data): \stdClass
    {
        foreach (GridService::TO_TRANSLATE_OPTIONS as $toTranslateOption) {
            if (isset($data->arguments->{$toTranslateOption})) {
                $data->arguments->{$toTranslateOption} = $this->translator->trans(
                    $data->arguments->{$toTranslateOption},
                    [],
                    TranslationDomain::DASHBOARD_GRID
                );
            }
        }

        return $data;
    }

    protected function handleArguments(Arguments $arguments, array $recordIds, ?string $recordType, array $params): void
    {
        if (isset($arguments->flowId)) {
            $action = new FlowAction([
                'event' => FlowAction::EVENT_INIT,
                'recordIds' => $recordIds,
            ]);

            $model = $arguments->params->model ?? new Model([]);
            if (!$model instanceof Model) {
                $model = new Model($model);
            }

            $response = $this->flowEventDispatcher->dispatch(
                $arguments->flowId,
                $action,
                $model,
                null,
                $params,
                $recordType,
                $arguments->flowAction ??  null
            );
            $properties = [
                'grid',
                'errors',
                'form',
                'model',
                'progress',
                'step',
                'guidance',
                'suggestions',
            ];
            foreach ($properties as $property) {
                if ($property === 'progress') {
                    $getter = 'getSteps';
                } elseif ($property === 'step') {
                    $getter = 'getCurrentStep';
                } else {
                    $getter = 'get' . \ucfirst($property);
                }
                if ($value = $response->$getter()) {
                    if ($property === 'progress') {
                        $value = [
                            'steps' => $value,
                        ];
                    }
                    $arguments->$property = $value;
                }
            }

            if (!empty($arguments->confirmCommandKey) && isset($arguments->model)) {
                $arguments->model->{Dwp::FLAG_CONFIRM_ACTION_KEY} = $arguments->confirmCommandKey;
                unset($arguments->confirmCommandKey);
            }
        }
    }
}
