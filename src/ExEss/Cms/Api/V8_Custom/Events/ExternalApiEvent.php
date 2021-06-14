<?php

namespace ExEss\Cms\Api\V8_Custom\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines an external event send through an external api, could be outgoing as well as incoming.
 */
class ExternalApiEvent extends Event
{
    private string $uri;

    private array $options;

    private static array $resolversByClass = [];

    private function __construct(string $uri, array $options)
    {
        $this->uri = $uri;

        // get the class name
        $class = static::class;

        // store resolver in object for possible re-usage
        if (!\array_key_exists($class, self::$resolversByClass)) {
            $optionsResolver = new OptionsResolver();
            $optionsResolver->setDefined(\array_keys($options));
            self::$resolversByClass[$class] = $optionsResolver;
            $this->configureOptions(self::$resolversByClass[$class]);
        }

        $this->options = self::$resolversByClass[$class]->resolve($options);
    }

    public static function withUri(string $uri, array $options = []): self
    {
        return new self($uri, $options);
    }

    /**
     * Defines the requirements for the options of the event
     *
     *
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException When options are not accessible.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException When undefined options are passed.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('method', '')
            ->setAllowedTypes('method', ['null', 'string']);

        $resolver
            ->setDefault('status', 200)
            ->setAllowedTypes('status', ['null', 'integer']);

        $resolver
            ->setDefault('data', '')
            ->setAllowedTypes('data', ['null', 'string']);

        $resolver
            ->setDefault('headers', [])
            ->setAllowedTypes('headers', ['array']);

        $resolver
            ->setDefault('channel', 'default')
            ->setAllowedTypes('channel', ['null', 'string']);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): ?string
    {
        return $this->options['method'];
    }

    public function getStatus(): ?int
    {
        return $this->options['status'];
    }

    public function getData(): ?string
    {
        return $this->options['data'];
    }

    public function getHeaders(): array
    {
        return $this->options['headers'];
    }

    public function setExtraHeaders(array $headers): void
    {
        $this->options['headers'] = \array_merge(
            $this->options['headers'],
            $headers
        );
    }

    public function getChannel(): ?string
    {
        return $this->options['channel'];
    }
}
