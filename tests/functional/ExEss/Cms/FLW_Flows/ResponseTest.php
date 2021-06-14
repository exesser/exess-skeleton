<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows;

use ExEss\Cms\Entity\FlowStep;
use stdClass;
use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\FLW_Flows\Action\Arguments;
use ExEss\Cms\FLW_Flows\Action\Command;
use ExEss\Cms\FLW_Flows\Response;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class ResponseTest extends FunctionalTestCase
{
    private Response $response;

    protected function _before(): void
    {
        $this->response = new Response();
    }

    public function testEmpty(): void
    {
        $this->tester->assertSame('{}', \json_encode($this->response));
    }

    public function testErrors(): void
    {
        $errors = new stdClass();
        $errors->foo = new stdClass();
        $errors->foo->bar = 'foo-bar';

        $this->response->setErrors($errors);

        $expected = <<<JSON
{
    "errors": {
        "foo": {
            "bar": "foo-bar"
        }
    }
}
JSON;

        $this->tester->assertJsonStringEqualsJsonString($expected, \json_encode($this->response));
    }

    public function testCurrentStep(): void
    {
        $nextStep = new Response\NextStep();
        $currentStep = new Response\CurrentStep(
            true,
            $nextStep,
            new FlowStep()
        );

        $this->response->setCurrentStep($currentStep);

        $expected = <<<JSON
{}
JSON;
        $this->tester->assertEquals($expected, \json_encode($this->response, \JSON_PRETTY_PRINT));

        $step = new Response\ProgressStep('id', 'key', 'name', 'type');
        $steps = new ObjectCollection(Response\ProgressStep::class, [$step]);
        $this->response->setSteps($steps);
        $expected = <<<JSON
{
    "progress": {
        "steps": [
            {
                "active": false,
                "canBeActivated": true,
                "disabled": false,
                "progressPercentage": 50,
                "valid": {
                    "result": true,
                    "errors": []
                },
                "id": "id",
                "key_c": "key",
                "name": "name",
                "type_c": "type"
            }
        ]
    },
    "step": {
        "willSave": true,
        "done": null,
        "next": {
            "nextStep": null,
            "actionId": null,
            "recordId": null,
            "lastStep": false,
            "mainMenuKey": null
        }
    }
}
JSON;
        $this->tester->assertJsonStringEqualsJsonString($expected, \json_encode($this->response));
    }

    public function testCommand(): void
    {
        $arguments = new Arguments();
        $arguments->stepId = 'bar';

        $command = new Command('foo', $arguments);

        $this->response->setCommand($command);

        $expected = <<<JSON
{
    "command": {
        "command": "foo",
        "arguments": {
            "stepId": "bar"
        },
        "relatedBeans": [],
        "params": []
    },
    "processCommand": true
}
JSON;
        $this->tester->assertJsonStringEqualsJsonString($expected, \json_encode($this->response));
    }

    public function testSteps(): void
    {
        $steps = new ObjectCollection(Response\ProgressStep::class);
        $steps[] = new Response\ProgressStep('step-id', 'step-key', 'step-name', 'step-type');

        $this->response->setSteps($steps);

        $expected = <<<JSON
{
    "progress": {
        "steps": [
            {
                "active": false,
                "canBeActivated": true,
                "disabled": false,
                "progressPercentage": 50,
                "valid": {
                    "result": true,
                    "errors": []
                },
                "id": "step-id",
                "key_c": "step-key",
                "name": "step-name",
                "type_c": "step-type"
            }
        ]
    }
}
JSON;
        $this->tester->assertJsonStringEqualsJsonString($expected, \json_encode($this->response));
    }
}
