<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FLW_Flows\Event\Listeners;

use ExEss\Cms\Api\V8_Custom\Events\FlowEvent;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Entity\FlowField;
use ExEss\Cms\FLW_Flows\Event\Listeners\ValidationSubscriber;
use ExEss\Cms\FLW_Flows\FlowValidator;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\FLW_Flows\Response\ValidationResult;
use Helper\Testcase\FunctionalTestCase;

class ValidationSubscriberTest extends FunctionalTestCase
{
    private ValidationSubscriber $subscriber;

    /**
     * @var FlowValidator|\Mockery\Mock
     */
    private $flowValidatorMock;

    public function _before(): void
    {
        $this->flowValidatorMock = \Mockery::mock(FlowValidator::class);
        $this->tester->mockService(FlowValidator::class, $this->flowValidatorMock);

        $this->subscriber = $this->tester->grabService(ValidationSubscriber::class);
    }

    public function testValidateFlow(): void
    {
        /** @var FlashMessageContainer $flashMessageContainer */
        $flashMessageContainer = $this->tester->grabService(FlashMessageContainer::class);
        $flashMessageContainer->reset();

        //mock
        $fieldName = new FlowField();
        $fieldName->setFieldId('account|name');

        $fieldAge = new FlowField();
        $fieldAge->setFieldId('account|age');
        $fieldAge->setHideExpression('1==1');

        $result = new ValidationResult();
        $result->setValid(false);
        $result->setFields([$fieldName, $fieldAge]);
        $result->setErrors([
            'account|name' => ['This should not be empty!'],
            'account|age' => ['This should not be empty!', 'Should be more then 18'],
            'aos_product_quotes' => [
                ['product_type' => ['Should be commodity!']],
                ['product_id' => ['This should not be empty!']]
            ],
        ]);

        $model = new Model(["child-field" => "value"]);
        $parentModel = new Model(["parent-field" => "value"]);

        $this->flowValidatorMock
            ->shouldReceive('validateFlow')
            ->once()
            ->with(
                \Mockery::type(Flow::class),
                \Mockery::on(function (Model $model) {
                    return \json_decode(\json_encode($model), true) === [
                            "child-field" => "value",
                            Dwp::PARENT_MODEL => ["parent-field" => "value"]
                        ];
                })
            )
            ->andReturn($result)
        ;

        $event = new FlowEvent('flow-key', new FlowAction(['event' => FlowAction::EVENT_INIT]), $model);
        $response = $event->getResponse();
        $response->setModel($model);
        $response->setParentModel($parentModel);

        $event->setFlow(new Flow());

        // run test
        $this->subscriber->validateFlow($event);

        $this->tester->assertCount(3, $flashMessageContainer->getFlashMessages());

        $this->tester->assertEquals(
            [
                'type' => FlashMessage::TYPE_ERROR,
                'text' => 'account|age: This should not be empty! Should be more then 18',
                'group' => 'account|age'
            ],
            $flashMessageContainer->getFlashMessages()[0]->jsonSerialize()
        );

        $this->tester->assertEquals(
            [
                'type' => FlashMessage::TYPE_ERROR,
                'text' => 'product_type: Should be commodity!',
                'group' => 'product_type'
            ],
            $flashMessageContainer->getFlashMessages()[1]->jsonSerialize()
        );

        $this->tester->assertEquals(
            [
                'type' => FlashMessage::TYPE_ERROR,
                'text' => 'product_id: This should not be empty!',
                'group' => 'product_id'
            ],
            $flashMessageContainer->getFlashMessages()[2]->jsonSerialize()
        );

        $this->tester->assertTrue($event->isPropagationStopped());

        $flashMessageContainer->reset();
    }
}
