<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Service;

use ExEss\Cms\Doctrine\Type\HttpMethod;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Body;
use ExEss\Cms\Api\V8_Custom\Service\RequestResponseLogger;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\Logger\Logger;
use ExEss\Cms\Test\Testcase\FunctionalTestCase;

class RequestResponseLoggerTest extends FunctionalTestCase
{
    private RequestResponseLogger $requestResponseLogger;

    /**
     * @var Logger|\Mockery\Mock
     */
    private $logger;

    public function _before(): void
    {
        $this->logger = \Mockery::mock(LoggerInterface::class);
        $this->tester->mockService('monolog.logger.request', $this->logger);
        $this->requestResponseLogger = $this->tester->grabService(RequestResponseLogger::class);
    }

    public function testRequestLoggerGet(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $method = HttpMethod::GET;
        $path = 'coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);

        $expected = "Incoming call for: $path, method: $method";

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logRequest($request);
    }

    public function testRequestLoggerFlow(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $method = HttpMethod::POST;
        $path = '/Api/V8_Custom/Flow/coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getParsedBody' => [
                'model' => ['test-model'],
                'action' => ['event' => FlowAction::EVENT_CONFIRM],
            ],
        ]);

        $expected = "Incoming call for: $path, method: $method, event: \"CONFIRM\", model: [\"test-model\"]";

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logRequest($request);
    }

    public function testRequestLoggerBody(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $method = HttpMethod::POST;
        $path = 'coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody' => $this->createBody('coole-body'),
        ]);

        $expected = "Incoming call for: $path, method: $method, body: coole-body";

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logRequest($request);
    }

    public function testRequestLoggerBigBody(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $method = HttpMethod::POST;
        $path = 'coole-url';

        $expectedBody = \str_repeat('X', RequestResponseLogger::MAX_MESSAGE_LENGTH);
        $longBody = $expectedBody . \str_repeat('X', 1000);

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody' => $this->createBody($longBody),
        ]);

        $expected = "Incoming call for: $path, method: $method, body: $expectedBody"
            . RequestResponseLogger::SUFFIX_TRUNCATED;

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logRequest($request);
    }

    public function testResponseLoggerGet(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $method = HttpMethod::GET;
        $path = 'coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
        ]);

        $expected = "Outgoing response for: $path, method: $method, status: 200, message: coole-message";

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logResponse($request, $response);
    }

    public function testResponseLoggerBody(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $method = HttpMethod::POST;
        $path = 'coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => $this->createBody('coole-body'),
        ]);

        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . "message: coole-message, body: coole-body";

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logResponse($request, $response);
    }

    public function testResponseLoggerBigBody(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $method = HttpMethod::POST;
        $path = 'coole-url';

        $expectedBody = \str_repeat('X', RequestResponseLogger::MAX_MESSAGE_LENGTH);
        $longBody = $expectedBody . \str_repeat('X', 1000);

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => $this->createBody($longBody),
        ]);

        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . "message: coole-message, body: $expectedBody" . RequestResponseLogger::SUFFIX_TRUNCATED;

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logResponse($request, $response);
    }

    public function testResponseLoggerModel(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $method = HttpMethod::POST;
        $path = '/Api/V8_Custom/Flow/coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => $this->createBody(\json_encode(['data' => ['model' => ['cool-model']]])),
        ]);

        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . 'message: coole-message, model: ["cool-model"]';

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logResponse($request, $response);
    }

    public function testResponseLoggerModelWithStream(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $method = HttpMethod::POST;
        $path = '/Api/V8_Custom/Flow/coole-url';

        $request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => $this->createBody(\json_encode(['data' => ['model' => [
                Dwp::BINARY_FILE => [
                    [
                        'name' => 'test',
                        'stream' => 'very-long-stream',
                    ]
                ]
            ]]])),
        ]);

        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . 'message: coole-message, model: {"dwp|binaryFile":[{"name":"test","stream":"stream-not-logged"}]}';

        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        $this->requestResponseLogger->logResponse($request, $response);
    }

    private function createBody(string $content): Body
    {
        $stream = \fopen('php://temp', 'r+');
        \fwrite($stream, $content);
        \fseek($stream, 0);

        return new Body($stream);
    }
}
