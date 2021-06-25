<?php
namespace ExEss\Cms\Config\Cache;

use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Component\ExpressionParser\ParserService;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ConfigCacheFactory
{
    private array $skipped = [
        \LIST_dynamic_list::class => [
            [
                'source' => \FLTRS_Filters::class,
                'relation' => 'fltrs_filters_dash_dashboard_1',
                'target' => null,
            ],[
                'source' => \FLW_Actions::class,
                'relation' => 'flw_actions_flw_guidancefields_1',
                'target' => null,
            ],
        ],
        \FLW_Flows::class => [
            [
                'source' => \FLW_Actions::class,
                'relation' => '*',
                'target' => null,
            ],[
                'source' => \FLW_GuidanceFieldValidators::class,
                'relation' => 'grid_panels_flw_guidancefieldvalidators_1',
                'target' => null,
            ],
        ],
    ];

    private int $validatorConditionsCounter = 0;

    private AdapterInterface $cache;

    private ParserService $parserService;

    private bool $cacheEnabled;

    private Security $security;

    public function __construct(
        bool $cacheEnabled,
        AdapterInterface $cache,
        ParserService $parserService,
        Security $security
    ) {
        $this->cache = $cache;
        $this->parserService = $parserService;
        $this->cacheEnabled = $cacheEnabled;
        $this->security = $security;
    }

    // @todo re-implement
    // /**
    //  * @throws \InvalidArgumentException When the fat entity id is not set.
    //  */
    // public function get(\AbstractFatEntity $configFatEntity): \AbstractFatEntity
    // {
    //     $userId = $this->security->getCurrentUserId() ?? 'anonymous';
    //
    //     $configFatEntityId = $configFatEntity->id;
    //     if (empty($configFatEntityId)) {
    //         throw new \InvalidArgumentException(\get_class($configFatEntity) . ' should not have an empty id.');
    //     }
    //
    //     $item = $this->cacheEnabled ? $this->cache->getItem($configFatEntityId . '_' . $userId) : null;
    //     // if ($item && $item->isHit()) {
    //     //     $cachedFatEntity = $this->serializer->deserialize($item->get(), \get_class($configFatEntity));
    //     // } else {
    //         $expressions = [];
    //         $skipped = $this->skipped[\get_class($configFatEntity)] ?? [];
    //         $this->getAllRelationsFor($expressions, $configFatEntity, $skipped);
    //
    //         $cachedFatEntity = $this->listHelperFunctions->parseListQuery(
    //             $configFatEntity,
    //             new ExpressionGroup(\implode(' ', $expressions)),
    //             (new PathResolverOptions())->setAllBeans([[$configFatEntityId]])
    //         )[$configFatEntityId];
    //
    //     //     if ($item) {
    //     //         $item->set($this->serializer->serialize($cachedFatEntity));
    //     //         $this->cache->save($item);
    //     //     }
    //     // }
    //
    //     return $cachedFatEntity;
    // }
    //
    // protected function getAllRelationsFor(
    //     array &$toLoad,
    //     \AbstractFatEntity $fatEntity,
    //     array &$processed,
    //     string $generalPrefix = ''
    // ): void {
    //     $source = \get_class($fatEntity);
    //     $metadata = $this->metadataFactory->getMetadataFor($source);
    //     $relations = $this->getConfigUsableRelations($metadata, $fatEntity);
    //
    //     // add this fat entity's fields
    //     $toLoad[] = "%{$generalPrefix}id%";        // always select the id first
    //     foreach ($metadata->getFields() as $field => $varDef) {
    //         if (($varDef['source'] ?? 'db') === 'db'
    //             && !\in_array(
    //                 $field,
    //                 ['id', 'date_entered', 'date_modified', 'modified_user_id', 'created_by'],
    //                 true
    //             )
    //         ) {
    //             $toLoad[] = "%{$generalPrefix}{$field}%";
    //         }
    //     }
    //
    //     // mark the relations that will be processed on this level
    //     foreach ($relations as $relation => $target) {
    //         // validator conditions must always be followed twice
    //         if ($relation === 'validator_conditions') {
    //             $this->validatorConditionsCounter++;
    //             if ($this->validatorConditionsCounter > Validator::NESTING_LEVEL_ALLOWANCE) {
    //                 $this->validatorConditionsCounter = 0;
    //                 unset($relations[$relation]);
    //                 continue;
    //             }
    //         }
    //         if ($relation !== 'validator_conditions'
    //             &&
    //             (
    //                 \in_array(['source' => $source, 'relation' => $relation, 'target' => $target],
    // $processed, false)
    //                 || \in_array(['source' => null, 'relation' => $relation, 'target' => $target],
    // $processed, false)
    //                 || \in_array(['source' => $source, 'relation' => $relation, 'target' => null],
    // $processed, false)
    //                 || \in_array(['source' => $target, 'relation' => $relation, 'target' => $source],
    // $processed, false)
    //                 || \in_array(['source' => $source, 'relation' => '*', 'target' => null], $processed, false)
    //                 || \in_array(['source' => null, 'relation' => '*', 'target' => $target], $processed, false)
    //             )
    //         ) {
    //             unset($relations[$relation]);
    //             continue;
    //         }
    //         $processed[] = [
    //             'source' => $source,
    //             'relation' => $relation,
    //             'target' => $target,
    //         ];
    //     }
    //
    //     foreach ($relations as $relation => $target) {
    //         $this->getAllRelationsFor(
    //             $toLoad,
    //             $this->fatEntityManager->newFatEntitySafe($target),
    //             $processed,
    //             "{$generalPrefix}{$relation}[]|"
    //         );
    //     }
    // }
    //
    // private function getConfigUsableRelations(ClassMetadata $metadata, \AbstractFatEntity $fatEntity): array
    // {
    //     $relations = [];
    //
    //     foreach ($metadata->getAssociationNames() as $relation) {
    //         $relatesTo = $fatEntity->loadRelationship($relation)->getRelatedMetadata()->getName();
    //         if (!\in_array($relatesTo, [User::class, \SecurityGroup::class], true)) {
    //             $relations[$relation] = $relatesTo;
    //         }
    //     }
    //
    //     return $relations;
    // }
}
