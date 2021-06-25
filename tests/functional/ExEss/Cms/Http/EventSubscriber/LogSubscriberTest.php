<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Http\EventSubscriber;

use ExEss\Cms\Doctrine\Type\HttpMethod;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Http\EventSubscriber\LogSubscriber;
use Mockery\MockInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\Logger\Logger;
use Helper\Testcase\FunctionalTestCase;

class LogSubscriberTest extends FunctionalTestCase
{
    private LogSubscriber $logSubscriber;

    /**
     * @var Logger|\Mockery\Mock
     */
    private $logger;

    /**
     * @var MockInterface|ServerRequestInterface
     */
    private $request;

    /**
     * @var MockInterface|ResponseInterface
     */
    private $response;

    public function _before(): void
    {
        $this->logger = \Mockery::mock(LoggerInterface::class);
        $this->tester->mockService('monolog.logger.request', $this->logger);

        $this->logSubscriber = $this->tester->grabService(LogSubscriber::class);

        $this->request = \Mockery::mock(ServerRequestInterface::class);
        $this->response = \Mockery::mock(ResponseInterface::class);
    }

    public function testRequestLoggerGet(): void
    {
        // Given
        $method = HttpMethod::GET;
        $path = 'cool-url';
        $expected = "Incoming call for: $path, method: $method";

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody->rewind' => true,
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrRequest($this->request);
    }

    public function testRequestLoggerFlow(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = '/Api/Flow/cool-url';
        $expected = "Incoming call for: $path, method: $method, event: \"CONFIRM\", model: [\"test-model\"]";

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody->rewind' => true,
            'getParsedBody' => [
                'model' => ['test-model'],
                'action' => ['event' => FlowAction::EVENT_CONFIRM],
            ],
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrRequest($this->request);
    }

    public function testRequestLoggerBody(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = 'cool-url';
        $expected = "Incoming call for: $path, method: $method, body: coole-body";

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody' => Stream::create('coole-body'),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrRequest($this->request);
    }

    public function testRequestLoggerBigBody(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = 'cool-url';
        $expectedBody = \str_repeat('X', LogSubscriber::MAX_MESSAGE_LENGTH);
        $expected = "Incoming call for: $path, method: $method, body: $expectedBody"
            . LogSubscriber::SUFFIX_TRUNCATED;

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
            'getBody' => Stream::create(
                $expectedBody . \str_repeat('X', 1000)
            ),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrRequest($this->request);
    }

    public function testResponseLoggerGet(): void
    {
        // Given
        $method = HttpMethod::GET;
        $path = 'cool-url';
        $expected = "Outgoing response for: $path, method: $method, status: 200, message: coole-message";

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $this->response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody->rewind' => true,
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrResponse($this->request, $this->response);
    }

    public function testResponseLoggerBody(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = 'cool-url';
        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . "message: coole-message, body: coole-body";

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $this->response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => Stream::create('coole-body'),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrResponse($this->request, $this->response);
    }

    public function testResponseLoggerBigBody(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = 'cool-url';
        $expectedBody = \str_repeat('X', LogSubscriber::MAX_MESSAGE_LENGTH);
        $longBody = $expectedBody . \str_repeat('X', 1000);
        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . "message: coole-message, body: $expectedBody" . LogSubscriber::SUFFIX_TRUNCATED;

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $this->response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => Stream::create($longBody),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrResponse($this->request, $this->response);
    }

    public function testResponseLoggerModel(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = '/Api/Flow/cool-url';
        $expected = "Outgoing response for: $path, method: $method, status: 200, "
            . 'message: coole-message, model: ["cool-model"]';

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $this->response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => Stream::create(\json_encode(['data' => ['model' => ['cool-model']]])),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrResponse($this->request, $this->response);
    }

    public function testResponseLoggerModelWithStream(): void
    {
        // Given
        $method = HttpMethod::POST;
        $path = '/Api/Flow/cool-url';
        $expected = "Outgoing response for: $path, method: $method, status: 200, message: coole-message, "
            . 'model: {"dwp|binaryFile":[{"name":"test","stream":"' . DataCleaner::STREAM_REPLACEMENT.'"}]}';

        // Then
        $this->request->shouldReceive([
            'getMethod' => $method,
            'getUri->getPath' => $path,
        ]);
        $this->response->shouldReceive([
            'getStatusCode' => 200,
            'getReasonPhrase' => 'coole-message',
            'getBody' => Stream::create(\json_encode(['data' => ['model' => [
                Dwp::BINARY_FILE => [
                    [
                        'name' => 'test',
                        'stream' => 'very-long-stream',
                    ]
                ]
            ]]])),
        ]);
        $this->logger
            ->shouldReceive('info')
            ->once()
            ->with($expected);

        // When
        $this->logSubscriber->logPsrResponse($this->request, $this->response);
    }
}
