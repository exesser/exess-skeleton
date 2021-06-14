<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Serializer\Normalizer;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use ExEss\Cms\Component\Client\Response\Response;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

/**
 * Denormalizes the guzzle message contents
 */
class GuzzleResponseNormalizer implements ContextAwareDenormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $type === Response::class && $format === GuzzleResponse::class;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @var GuzzleResponse $data */
        return new Response(
            $data->getStatusCode(),
            $data->getHeaders(),
            $data->getBody()->getContents()
        );
    }
}
