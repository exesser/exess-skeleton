<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Logger\Processor;

class EnvironmentProcessor
{
    private ?string $tag = null;

    public function __construct(?string $tag = null)
    {
        $this->tag = $tag;
    }

    public function __invoke(array $record): array
    {
        if (null !== $this->tag) {
            $record['extra']['tag'] = $this->tag;
        }

        return $record;
    }
}
