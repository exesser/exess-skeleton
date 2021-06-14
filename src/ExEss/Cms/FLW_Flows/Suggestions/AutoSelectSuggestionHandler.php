<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\GeneratedFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;

class AutoSelectSuggestionHandler extends AbstractSuggestionHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $response->getModel()
            && $response->getSuggestions()->getAll()->count()
        ;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $oneSuggestionOnly = [];
        foreach ($response->getSuggestions()->getAll() as $fieldKey => $fieldSuggestions) {
            if ($fieldSuggestions->count() === 1 && empty($response->getModel()->$fieldKey)) {
                $oneSuggestionOnly[$fieldKey] = $fieldSuggestions[0];
            }
        }

        if (empty($oneSuggestionOnly)) {
            return;
        }

        $fieldsToUpdate = $this->getFromDBTheFieldsThatWeNeedToUpdate($flow->getKey(), \array_keys($oneSuggestionOnly));

        foreach ($fieldsToUpdate as $fieldKey => $options) {
            $this->updateModelForField(
                $response->getModel(),
                $fieldKey,
                $oneSuggestionOnly[$fieldKey],
                $options['valueIsArray']
            );

            if ($options['repeatTrigger']) {
                $response->setForceReload(true);
            }
        }
    }

    private function getFromDBTheFieldsThatWeNeedToUpdate(string $flowKey, array $fieldKeys): array
    {
        $qb = $this->em->getRepository(Flow::class)->createQueryBuilder('f');
        $expr = $qb->expr();

        $qb
            ->select("gf.fieldId, gf.multiple, gf.generatedType")
            ->join("f.stepLinks", 'fsl')
            ->join("fsl.flowStep", "fs")
            ->join("fs.fields", "gf")
            ->andWhere($expr->in("gf.fieldId", $fieldKeys))
            ->andWhere($expr->eq("gf.autoSelectSuggestions", true))
            ->andWhere($expr->eq("f.key", ':flow'))
            ->setParameter('flow', $flowKey)
        ;

        $fieldsToUpdate = [];
        foreach ($qb->getQuery()->execute() as $row) {
            $fieldsToUpdate[$row['fieldId']] = [
                'valueIsArray' => (bool) $row['multiple'],
                'repeatTrigger' => $row['generatedType'] === GeneratedFieldType::REPEAT_TRIGGER,
            ];
        }

        return $fieldsToUpdate;
    }

    private function updateModelForField(
        Model $model,
        string $fieldKey,
        Response\Suggestion\SuggestionInterface $suggestion,
        bool $isArray
    ): void {
        if ($suggestion instanceof Response\Suggestion\ValueSuggestion) {
            $model->$fieldKey = $isArray ? [$suggestion->getValue()] : $suggestion->getValue();
        }

        if ($suggestion instanceof Response\Suggestion\ModelSuggestion) {
            foreach ($suggestion->getModel() as $modelFieldKey => $modelFieldValue) {
                $model->$modelFieldKey = $modelFieldValue;
            }
        }
    }
}
