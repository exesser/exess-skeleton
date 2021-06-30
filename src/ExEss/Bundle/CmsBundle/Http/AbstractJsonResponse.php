<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractJsonResponse extends JsonResponse
{
    /**
     * @param array|FlashMessage[] $message
     */
    public function addFlashMessages(array $message): void
    {
        $data = DataCleaner::jsonDecode($this->data);
        $data['flashMessages'] = $message;

        $this->setData($data);
    }
}
