<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Cms\Doctrine\Type\FlowFieldType;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Service\SelectWithSearchService;

class SelectWithSearchLabelHandler extends AbstractSuggestionHandler
{
    protected EntityManagerInterface $em;
    protected SelectWithSearchService $selectWithSearchService;

    public function __construct(EntityManagerInterface $em, SelectWithSearchService $selectWithSearchService)
    {
        $this->em = $em;
        $this->selectWithSearchService = $selectWithSearchService;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $response->getModel()
            && !empty($response->getModel()->toArray())
        ;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $model = $response->getModel();

        $modelFields = \array_keys($model->toArray());

        $qb = $this->em->getRepository(Flow::class)->createQueryBuilder('f');
        $expr = $qb->expr();

        $qb
            ->select("gf.fieldId, gf.custom")
            ->join("f.stepLinks", 'fsl')
            ->join("fsl.flowStep", "fs")
            ->join("fs.fields", "gf")
            ->andWhere($expr->in("gf.fieldId", $modelFields))
            ->andWhere($expr->isNotNull("gf.custom"))
            ->andWhere($expr->eq("gf.type", ":fieldType"))
            ->andWhere($expr->eq("f.key", ':flow'))
            ->setParameter('fieldType', FlowFieldType::FIELD_TYPE_SELECT_WITH_SEARCH)
            ->setParameter('flow', $flow->getKey())
        ;

        foreach ($qb->getQuery()->execute() as $row) {
            $this->updateSelectWithSearchLabel($model, $row['fieldId'], $row['custom']);
        }
    }

    private function updateSelectWithSearchLabel(Model $model, string $fieldId, array $custom): void
    {
        $items = $model->$fieldId;
        if (empty($items)) {
            return;
        }

        $items = $this->transformItemsToRequestedFormat($items);

        if (!$this->shouldUpdateLabel($items)) {
            return;
        }

        $model->$fieldId = $this->selectWithSearchService->getLabelsForValues(
            $custom['datasourceName'],
            \array_map(function ($item) {
                return $item['key'];
            }, $items),
            $model
        );
    }

    private function shouldUpdateLabel(array $items): bool
    {
        foreach ($items as $item) {
            if (empty($item['label'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $items
     * @return array
     */
    private function transformItemsToRequestedFormat($items): array
    {
        $items = DataCleaner::jsonDecode(\json_encode($items));
        if (\is_array($items)) {
            foreach ($items as $itemKey => $itemValue) {
                if (!\is_array($itemValue)) {
                    $items[$itemKey] = ['key' => $itemValue];
                }
            }
            return $items;
        }

        return [['key' => $items]];
    }
}
