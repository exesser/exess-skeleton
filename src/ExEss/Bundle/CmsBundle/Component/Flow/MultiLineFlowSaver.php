<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow;

use Doctrine\ORM\EntityManager;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\AfterSave\AfterSaveData;
use ExEss\Bundle\CmsBundle\Component\Flow\Response\Model;
use ExEss\Bundle\CmsBundle\Service\GridService;

class MultiLineFlowSaver
{
    private GridService $gridService;

    private SaveFlow $saveFlow;

    private EntityManager $em;

    public function __construct(
        EntityManager $em,
        GridService $gridService,
        SaveFlow $saveFlow
    ) {
        $this->gridService = $gridService;
        $this->saveFlow = $saveFlow;
        $this->em = $em;
    }

    /**
     * @throws \InvalidArgumentException No childModel found for repeatKey or no child models at all.
     */
    public function save(Flow $parentFlow, Model $parentModel): AfterSaveData
    {
        // get the repeatable rows
        $gridRepo = $this->gridService;

        $repeatedNameSpaces = [];
        $childFlowsToSave = [];
        foreach ($gridRepo->getRepeatableRows($parentFlow) as $repeatableRow) {
            $options = $repeatableRow->getOptions();
            $modelKey = $options->getModelKey();

            if (\in_array($modelKey, $repeatedNameSpaces, true)) {
                // never mind, this will be saved already
                continue;
            }
            $repeatedNameSpaces[] = $modelKey;

            if (!$parentModel->$modelKey instanceof Model) {
                throw new \InvalidArgumentException('No childModels found for namespace:' . $modelKey);
            }
            $childModels = $parentModel->$modelKey;
            $parentModel->clearNamespace($modelKey);

            $childFlow = $this->em->getRepository(Flow::class)->get($options->getFlowId());

            foreach ($gridRepo->getRepeatValuesFromModel($repeatableRow, $parentModel) as $repeatKey) {
                if (!$childModels->$repeatKey instanceof Model) {
                    throw new \InvalidArgumentException('No childModel found for repeatKey:' . $repeatKey);
                }
                $childFlowsToSave[] = [$childFlow, $childModels->$repeatKey, $repeatKey];
            }
        }

        // save the parent flow
        $parentFlowData = $this->saveFlow->save($parentFlow, $parentModel);
        $afterSaveData = new AfterSaveData($parentFlowData);

        // merge the results
        /**
         * @var Flow $childFlow
         * @var Model $childModel
         */
        foreach ($childFlowsToSave as [$childFlow, $childModel, $repeatKey]) {
            $parentBeans = $parentFlowData->getEntities();
            // unset any fat entities from the base type of this flow since they might interfere
            if (!empty($childFlow->getBaseObject()) && isset($parentBeans[$childFlow->getBaseObject()])) {
                $spareBeans = $parentBeans[$childFlow->getBaseObject()];
                unset($parentBeans[$childFlow->getBaseObject()]);
            }
            // save the child flow
            $childFlowData = $this->saveFlow->save($childFlow, $childModel, $parentModel, $parentBeans);
            if (!$parentModel->offsetExists($modelKey)) {
                $parentModel->setFieldValue($modelKey, new Model());
            }
            $parentModel->$modelKey->$repeatKey = $childModel;

            if (!empty($spareBeans)) {
                if (!isset($parentBeans[$childFlow->getBaseObject()])) {
                    $parentBeans[$childFlow->getBaseObject()] = $spareBeans;
                } else {
                    foreach ($spareBeans as $spareBean) {
                        $parentBeans[$childFlow->getBaseObject()][] = $spareBean;
                    }
                }
            }

            $afterSaveData->addSubFlowData($childFlowData);
        }

        return $afterSaveData;
    }
}
