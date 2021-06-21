<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use ExEss\Cms\Dashboard\Model\Grid;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowStep;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Service\GridService;
use Helper\Testcase\FunctionalTestCase;

class GridServiceTest extends FunctionalTestCase
{
    public const FIELD_NAME = 'userId';

    private GridService $gridService;

    private const FLOW = 'my-flow';

    private const FLOW_STEP = 'my-flowstep';

    public function _before(): void
    {
        $this->gridService = $this->tester->grabService(GridService::class);

        $this->tester->generateUser("user 1", ["id" => "123"]);
        $this->tester->generateUser("user 2", ["id" => "abc"]);

        $this->tester->loadJsonFixturesFrom(__DIR__ . '/resources/repeatable.fixtures.json');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFindNextStepGridWithRepeatable(Model $model, string $expectedModelFile): void
    {
        // arrange
        $flow = $this->tester->grabEntityFromRepository(Flow::class, ['id' => self::FLOW]);
        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => self::FLOW_STEP]);

        // act
        $grid = $this->gridService->getGridForFlowStep($flowStep, $model, $flow);

        // assert
        $this->tester->assertInstanceOf(Grid::class, $grid);
        $expectedModel = \json_decode(\file_get_contents(__DIR__ . "/resources/$expectedModelFile"), true);
        $this->tester->assertEquals(\json_encode($expectedModel), \json_encode($grid));
    }

    public function dataProvider(): array
    {
        $noneSelected = new Model();

        $oneSelected = new Model();
        $oneSelected->{self::FIELD_NAME} = ['abc',];

        $oneSelectedString = new Model();
        $oneSelectedString->{self::FIELD_NAME} = 'abc';

        $twoSelected = new Model();
        $twoSelected->{self::FIELD_NAME} = ['123', 'abc',];

        return [
            [$noneSelected, 'repeatable-0.model.json',],
            [$oneSelectedString, 'repeatable-1.model.json',],
            [$oneSelected, 'repeatable-1.model.json',],
            [$twoSelected, 'repeatable-2.model.json',],
        ];
    }

    public function testFlowStepHasRepeatableRow(): void
    {
        // arrange
        $flowStep = $this->tester->grabEntityFromRepository(FlowStep::class, ['id' => self::FLOW_STEP]);

        // act, assert
        $this->tester->assertTrue($this->gridService->hasFlowRepeatableRowsInStep($flowStep));
    }

    public function testFlowHasRepeatableRow(): void
    {
        // arrange
        $flow = $this->tester->grabEntityFromRepository(Flow::class, ['id' => self::FLOW]);

        // act, assert
        $this->tester->assertTrue($this->gridService->hasFlowRepeatableRows($flow));
    }
}
