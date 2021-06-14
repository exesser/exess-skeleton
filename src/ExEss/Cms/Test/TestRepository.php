<?php declare(strict_types=1);

namespace ExEss\Cms\Test;

use ExEss\Cms\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Cms\Base\Response\BaseListResponse;

class TestRepository extends AbstractRepository
{
    public const NAME = 'testHandler';

    public function getRequest(array $requestData): void
    {
        // TODO: Implement getRequest() method.
    }

    public function findBy(array $requestData): BaseListResponse
    {
        $rows = [
            'list' => [
                \json_decode(\json_encode([
                    'id' => 12345678,
                    'agentBlock' => true,
                    'billingAccount' => 'billingAccount',
                    'eans' => ['eans'],
                    'journal' => 'journal',
                    'journalType' => 'journalType',
                    'lastRejectReason' => 'lastRejectReason',
                    'linkedInvoice' => 'linkedInvoice',
                    'openAmount' => 0,
                    'paymentMethod' => 'paymentMethod',
                    'paymentPlan' => true,
                    'reference' => 'reference',
                    'rejectCount' => 0,
                    'status' => 'status',
                    'totalAmount' => 0,
                    'type' => 'type',
                    'paymentReference' => 'paymentReference',
                    'accountId' => null,
                    'dunningStatus' => 'blub',
                    'allowInPaymentPlan' => true,
                    'allowToReverseDunningCost' => true,
                ])),
            ],
        ];

        /** @var BaseListResponse|\Mockery\Mock $response */
        $response = \Mockery::mock(BaseListResponse::class);
        $response->shouldReceive('getResult')->once()->andReturn(true);
        $response->shouldReceive('getResponse')->once()->andReturn($rows);

        return $response;
    }
}
