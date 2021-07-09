<?php declare(strict_types=1);

namespace Helper;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\AbstractRepository;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;

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
                DataCleaner::jsonDecode(\json_encode([
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
                ]), false),
            ],
        ];

        /** @var BaseListResponse|\Mockery\Mock $response */
        $response = \Mockery::mock(BaseListResponse::class);
        $response->shouldReceive('getResult')->once()->andReturn(true);
        $response->shouldReceive('getResponse')->once()->andReturn($rows);

        return $response;
    }
}
