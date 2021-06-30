<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Http\EventSubscriber;

use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use ExEss\Bundle\CmsBundle\Http\ErrorResponse;
use ExEss\Bundle\CmsBundle\Http\EventSubscriber\ExceptionSubscriber;
use ExEss\Bundle\CmsBundle\Exception\NotAllowedException;
use ExEss\Bundle\CmsBundle\Exception\NotFoundException;
use Helper\Testcase\FunctionalTestCase;

class ExceptionSubscriberTest extends FunctionalTestCase
{
    private ExceptionSubscriber $exceptionSubscriber;

    public function _before(): void
    {
        $this->exceptionSubscriber = $this->tester->grabService(ExceptionSubscriber::class);
    }

    /**
     * @dataProvider exceptionProvider
     */
    public function testHandlesException(int $status, string $type, string $exception): void
    {
        // Given
        $exceptionMessage = 'test_msg';
        /** @var \Throwable $e */
        $e = new $exception($exceptionMessage);

        // When
        $response = $this->exceptionSubscriber->transformToResponse($e);

        // Then
        $body = DataCleaner::jsonDecode((string) $response->getContent());

        // Check status
        $this->tester->assertEquals($status, $response->getStatusCode(), "Response should have $status status");

        // Check message
        $message = $body['message'] ?? null;
        $this->tester->assertTrue(isset($message), 'Message key should be present');
        $this->tester->assertEquals($message, ErrorResponse::MESSAGE_ERROR);

        // Check data
        $data = $body['data'] ?? null;
        $this->tester->assertTrue(isset($data), 'Data key should be present');
        $this->tester->assertTrue(\is_array($data), 'Data should be an array');
        $this->tester->assertNotEmpty($data, 'Data should not be empty');

        $this->tester->assertEquals($data['type'], $type, "Error type should be $type");
        $this->tester->assertEquals($data['message'], $exceptionMessage, "Message should be correct");
    }

    public function exceptionProvider(): array
    {
        return [
            '404 for NotFoundException' =>
                [404, ErrorResponse::TYPE_NOT_FOUND_EXCEPTION, NotFoundException::class],
            '405 for NotAllowedException' =>
                [405, ErrorResponse::TYPE_NOT_ALLOWED_EXCEPTION, NotAllowedException::class],
            '422 for LogicException' =>
                [422, ErrorResponse::TYPE_DOMAIN_EXCEPTION, \LogicException::class],
            '422 for DomainException' =>
                [422, ErrorResponse::TYPE_DOMAIN_EXCEPTION, \DomainException::class],
            '500 for anything else' =>
                [500, ErrorResponse::TYPE_FATAL_ERROR, \Exception::class],
        ];
    }
}
