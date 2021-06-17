<?php declare(strict_types=1);

namespace ExEss\Cms\Http;

use ExEss\Cms\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractJsonResponse extends JsonResponse
{
    /**
     * @param array|FlashMessage[] $message
     */
    public function addFlashMessages(array $message): void
    {
        $data = \json_decode($this->data, true);
        $data['flashMessages'] = $message;

        $this->setData($data);
    }
}
