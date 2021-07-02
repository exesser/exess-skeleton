<?php
namespace App\Robo\Task\Db\Release\Export;

use Robo\Result;

class DwpConfig extends AbstractDbExport
{
    private const DWP_CONFIG_MODULES = [
        \ConditionalMessage::class,
        \DASH_Dashboard::class,
        \DASH_DashboardMenu::class,
        \DASH_DashboardMenuActionGroup::class,
        \DASH_DashboardProperties::class,
        \DASH_MenuActions::class,
        \FE_SelectWithSearch::class,
        \FIND_Search::class,
        \FLTRS_Fields::class,
        \FLTRS_FieldsGroup::class,
        \FLTRS_Filters::class,
        \FLW_Actions::class,
        \FLW_Flows::class,
        \FLW_FlowStepProperties::class,
        \FLW_FlowSteps::class,
        \FLW_FlowStepsLink::class,
        \FLW_GuidanceFields::class,
        \FLW_GuidanceFieldValidators::class,
        \GRID_GridTemplates::class,
        \GRID_Panels::class,
        \LIST_Cell::class,
        \LIST_Cells::class,
        \LIST_dynamic_list::class,
        \LIST_external_object::class,
        \LIST_external_object_linkfields::class,
        \LIST_row_action::class,
        \LIST_row_bar::class,
        \LIST_sorting_options::class,
        \LIST_top_action::class,
        \LIST_topbar::class,
        \menu_MainMenu::class,
        \SecurityGroup_API::class,
    ];

    protected string $subPath = '/config';

    /**
     * @inheritdoc
     */
    public function runExport()
    {
        $fatEntities = $this->getFatEntitiesConfig(self::DWP_CONFIG_MODULES);

        if (\count($fatEntities) === 0) {
            return new Result($this, 1, 'no dwp configurable modules found');
        }

        $releaseNotesScope = $this->dumpBeanTables(
            $fatEntities,
            $this->subPath . '/release_config.sql'
        );
        $releaseNotesScope += $this->dumpRelationshipsTables(
            $fatEntities,
            $this->subPath . '/release_config_relationships.sql'
        );

        $this->dumpSecurityGroupTables($fatEntities, $this->subPath . '/release_config_securitygroups.sql');

        $this->generateReleaseNotes($releaseNotesScope);
    }

    /**
     * @return ClassMetadata[]|array
     */
    protected function getFatEntitiesConfig(array $classNames): array
    {
        /** @var ClassMetadataFactory $metadataFactory */
        $metadataFactory = $this->getContainer()->get(ClassMetadataFactory::class);

        return \array_map(
            function (string $className) use ($metadataFactory): ClassMetadata {
                return $metadataFactory->getMetadataFor($className);
            },
            $classNames
        );
    }
}
