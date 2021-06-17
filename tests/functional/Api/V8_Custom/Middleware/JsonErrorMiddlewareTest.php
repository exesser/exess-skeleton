<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Middleware;

use ExEss\Cms\Http\ErrorResponse;
use Mockery\Mock;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response as HttpResponse;
use ExEss\Cms\Api\V8_Custom\Middleware\JsonErrorMiddleware;
use ExEss\Cms\Exception\NotAllowedException;
use ExEss\Cms\Exception\NotFoundException;
use ExEss\Cms\Logger\Logger;
use Helper\Testcase\FunctionalTestCase;

class JsonErrorMiddlewareTest extends FunctionalTestCase
{
    private JsonErrorMiddleware $middleware;

    /**
     * @var Logger|Mock
     */
    private $logger;

    public function _before(): void
    {
        $this->logger = \Mockery::mock(Logger::class);
        $this->tester->mockService(Logger::class, $this->logger);

        $this->middleware = $this->tester->grabService(JsonErrorMiddleware::class);
    }

    /**
     * @dataProvider exceptionProvider
     */
    public function testHandlesException(int $status, string $type, string $loglvl, string $exception): void
    {
        // arrange
        $exceptionMessage = 'test_msg';
        /** @var \Exception $e */
        $e = new $exception($exceptionMessage);

        $this->logger
            ->shouldReceive($loglvl)
            ->with($exceptionMessage . \PHP_EOL . $e->getTraceAsString())
            ->once();

        // act
        /** @var HttpResponse $response */
        $response = ($this->middleware)(
            \Mockery::mock(ServerRequestInterface::class),
            new HttpResponse(),
            function () use ($e): void {
                throw $e;
            }
        );

        // assert
        $this->tester->assertInstanceOf(HttpResponse::class, $response, 'Response should be HttpResponse');

        $body = \json_decode((string) $response->getBody(), true);

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
                [404, ErrorResponse::TYPE_NOT_FOUND_EXCEPTION, 'warning', NotFoundException::class],
            '405 for NotAllowedException' =>
                [405, ErrorResponse::TYPE_NOT_ALLOWED_EXCEPTION, 'warning', NotAllowedException::class],
            '422 for LogicException' =>
                [422, ErrorResponse::TYPE_DOMAIN_EXCEPTION, 'error', \LogicException::class],
            '422 for DomainException' =>
                [422, ErrorResponse::TYPE_DOMAIN_EXCEPTION, 'error', \DomainException::class],
            '500 for anything else' =>
                [500, ErrorResponse::TYPE_FATAL_ERROR, 'critical', \Exception::class],
        ];
    }
}
