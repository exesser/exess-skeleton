<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Health\Serializer\Normalizer;

use ExEss\Cms\Component\Health\Model\HealthCheckResult;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class HealthCheckResultNormalizer implements ContextAwareNormalizerInterface
{
    public const HEALTH_CHECK = 'health_check';

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return isset($context[self::HEALTH_CHECK])
            && $context[self::HEALTH_CHECK];
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $xml = new \SimpleXMLElement('<checkResponse/>');
        $errorMessages = [];
        $ok = true;

        foreach ($object as $name => $result) {
            if (!$result->getResult()) {
                $ok = false;
                $errorMessages[] = $result->getMessage();
            }

            $checkResults = $xml->addChild('checkResults');
            $checkResults->addChild('checkDomain', 'CRM');
            $checkResults->addChild('subDomain', $name);
            $checkResults->addChild('result', $result->getResult() ? 'true' : 'false');
            $checkResults->addChild('message', $result->getMessage());
        }

        $xml->addChild('result', $ok ? 'true' : 'false');
        $xml->addChild('msg', $ok? HealthCheckResult::OK: \implode(', ', $errorMessages));

        return $xml;
    }
}
