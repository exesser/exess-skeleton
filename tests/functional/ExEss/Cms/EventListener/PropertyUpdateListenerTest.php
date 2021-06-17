<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use ExEss\Cms\Entity\Dashboard;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\Entity\GridTemplate;
use ExEss\Cms\Entity\User;
use Helper\Testcase\FunctionalTestCase;

/**
 * @see PropertyUpdateListener
 */
class PropertyUpdateListenerTest extends FunctionalTestCase
{
    private const PROPERTY_NAME_1 = 'recordId';
    private const PROPERTY_NAME_2 = 'secondRecordId';

    private const GRID_JSON = [
        "columns" => [
            "size" => "1-1",
            "hasMargin" => false,
            "rows" => [
                [
                    "size" => "",
                    "type" => "embeddedGuidance",
                    "cssClasses" => [
                        "card"
                    ]
                ],
                [
                    "type" => "list",
                    "options" => [
                        "listKey" => "test-1",
                        "params" => [
                            "recordId" => '%'.self::PROPERTY_NAME_1.'%'
                        ]
                    ]
                ],
                [
                    "size" => "",
                    "type" => "list",
                    "options" => [
                        "listKey" => "test-2",
                        "params" => [
                            "recordId" => '%'.self::PROPERTY_NAME_2.'%'
                        ]
                    ]
                ],

            ],
        ]
    ];

    private EntityManager $em;

    public function _before(): void
    {
        $this->em = $this->tester->grabService('doctrine.orm.entity_manager');
    }

    public function testDashboardUpdate(): void
    {
        // Given
        $dashboardId = $this->tester->generateDashboard([
            "grid_gridtemplates_id_c" => $this->tester->generateGrid([], self::GRID_JSON),
        ]);
        $this->tester->generateDashboardProperty($dashboardId, self::PROPERTY_NAME_1);
        $this->tester->generateDashboardProperty($dashboardId, 'blub');

        /** @var Dashboard $dashboard */
        $dashboard = $this->em->getRepository(Dashboard::class)->find($dashboardId);

        // When
        $dashboard->setName("test");
        $this->em->persist($dashboard);
        $this->em->flush();

        // Then
        $this->assertPropertyNames($dashboard->getProperties());
    }

    public function testFlowStepUpdate(): void
    {
        // Given
        $flowStepId = $this->tester->generateFlowSteps(
            $this->tester->generateFlow(),
            [
                "grid_template_id" => $this->tester->generateGrid([], self::GRID_JSON),
            ]
        );
        $this->tester->generateFlowStepProperty($flowStepId, self::PROPERTY_NAME_1);
        $this->tester->generateFlowStepProperty($flowStepId, 'blub');

        /** @var FlowStep $flowStep */
        $flowStep = $this->em->getRepository(FlowStep::class)->find($flowStepId);

        // When
        $flowStep->setName("test");
        $this->em->persist($flowStep);
        $this->em->flush();

        // Then
        $this->assertPropertyNames($flowStep->getProperties());
    }

    public function entityTypeProvider(): array
    {
        return [
            [FlowStep::class],
            [Dashboard::class],
        ];
    }

    /**
     * @dataProvider entityTypeProvider
     */
    public function testInsert(string $entityClass): void
    {
        // Given
        $gridTemplateId = $this->tester->generateGrid([], self::GRID_JSON);
        $gridTemplate = $this->em->getRepository(GridTemplate::class)->find($gridTemplateId);

        $user = $this->em->getRepository(User::class)->find(1);

        $entity = new $entityClass;
        $entity->setCreatedBy($user);
        $entity->setGridTemplate($gridTemplate);

        // When
        $this->em->persist($entity);
        $this->em->flush();

        // Then
        $this->assertPropertyNames($entity->getProperties());
    }

    private function assertPropertyNames(Collection $properties): void
    {
        $this->tester->assertCount(2, $properties);
        foreach ($properties as $property) {
            $this->tester->assertTrue(
                \in_array($property->getName(), [self::PROPERTY_NAME_1, self::PROPERTY_NAME_2], true)
            );
        }
    }
}
