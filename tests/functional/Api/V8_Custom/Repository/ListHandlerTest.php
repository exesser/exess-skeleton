<?php declare(strict_types=1);

namespace Test\Functional\Api\V8_Custom\Repository;

use Helper\TestRepository;
use ExEss\Cms\Api\V8_Custom\Repository\ListHandler;
use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Cms\Base\Response\BaseListResponse;
use ExEss\Cms\Exception\ExternalListFetchException;
use Helper\Testcase\FunctionalTestCase;

class ListHandlerTest extends FunctionalTestCase
{
    private const EXISTING_HANDLER_CLASS = TestRepository::class;

    private \Mockery\MockInterface $repository;

    public function _before(): void
    {
        $this->tester->mockService(
            self::EXISTING_HANDLER_CLASS,
            $this->repository = \Mockery::mock(self::EXISTING_HANDLER_CLASS)
        );
    }

    public function testGetList(): void
    {
        $result = ['foo'];

        $response = \Mockery::mock(BaseListResponse::class)->makePartial();
        $response->shouldReceive('getResult')->andReturn($result);
        $response->shouldReceive('jsonSerialize')->andReturn($result);
        $response->shouldReceive('getList')->andReturn($result);

        $this->repository
            ->shouldReceive('findBy')
            ->once()
            ->andReturn($response);

        $response = $this->tester
            ->grabService(ListHandler::class)
            ->getList(
                TestRepository::NAME,
                [
                    'params' => [],
                ]
            )
        ;

        $this->tester->assertArrayHasKey('list', $response);
        $this->tester->assertSame($response['list'], $result);

        $this->tester->assertArrayHasKey('total', $response);
        $this->tester->assertSame($response['total'], 1);
    }

    /**
     * @dataProvider testGetListFailChecksProvider
     *
     * @param string|\stdClass $handler
     */
    public function testGetListFailChecks(
        string $handlerName,
        $handler,
        ?string $responseClass,
        bool $throwsException
    ): void {
        $this->tester->mockService(self::EXISTING_HANDLER_CLASS, \Mockery::mock($handler));

        if ($throwsException) {
            $this->tester->expectThrowable(
                ExternalListFetchException::class,
                function () use ($handlerName): void {
                    $this->tester
                        ->grabService(ListHandler::class)
                        ->getList($handlerName, [])
                    ;
                }
            );
        } else {
            $response = \Mockery::mock($responseClass);
            $this->tester->grabService(self::EXISTING_HANDLER_CLASS)
                ->shouldReceive('findBy')
                ->once()
                ->andReturn($response)
            ;

            $response
                ->shouldReceive([
                    'getResult' => false,
                    'getMessage' => 'msg',
                    'getResponse' => [],
                ])
                ->once()
            ;

            $flashMsg = \Mockery::mock(FlashMessageContainer::class);
            $this->tester->mockService(FlashMessageContainer::class, $flashMsg);
            $flashMsg->shouldReceive('addFlashMessage')->once();

            $this->tester
                ->grabService(ListHandler::class)
                ->getList($handlerName, [])
            ;
        }
    }

    public function testGetListFailChecksProvider(): array
    {
        return [
            'Non existing handler' => [
                'handlerName' => 'blaBlaHandler',
                'handler' => self::EXISTING_HANDLER_CLASS,
                'responseClass' => \stdClass::class,
                'throwsException' => true,
            ],
            'Response is not a BaseListResponse' => [
                'handlerName' => TestRepository::NAME,
                'handlerClass' => self::EXISTING_HANDLER_CLASS,
                'responseClass' => \stdClass::class,
                'throwsException' => true,
            ],
            'Response is not ok' => [
                'handlerName' => TestRepository::NAME,
                'handlerClass' => self::EXISTING_HANDLER_CLASS,
                'responseClass' => BaseListResponse::class,
                'throwsException' => false,
            ],
        ];
    }
}
