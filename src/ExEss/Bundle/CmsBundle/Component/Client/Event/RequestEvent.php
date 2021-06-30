<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Event;

class RequestEvent extends AbstractEvent
{
    public function setExtraHeaders(array $headers): void
    {
        $this->options['headers'] = \array_merge($this->options['headers'] ?? [], $headers);
    }
}
