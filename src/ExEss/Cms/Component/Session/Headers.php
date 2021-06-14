<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Session;

use ExEss\Cms\Component\Session\User\UserInterface;
use Ramsey\Uuid\Uuid;

/**
 * A class to save and compute extra headers needed for external calls
 */
class Headers implements \ArrayAccess, \Countable, \IteratorAggregate
{
    public const LOG_ID = 'X-LOG-ID';
    public const LOG_DESCRIPTION = 'X-LOG-DESCRIPTION';
    public const LOG_COMPONENT = 'X-LOG-COMPONENT';
    public const LOG_SPAN = 'X-LOG-SPAN';
    public const LOG_PARENT_ID = 'X-LOG-PARENT-ID';
    public const DWP_FULL_PATH_HEADER = 'X-DWP-FULL-PATH';
    public const LOG_MODE = 'X-LOG-MODE';
    public const USER_LOG_ID_KEY = 'X-LOG-USER-ID';
    public const USER_LOG_KEY = 'X-LOG-USER';
    public const MESSAGE_ID = 'message-id';

    public const DEFAULT_COMPONENT = 'CRM';

    private static ?self $instance = null;

    protected array $data = [];

    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    /**
     * Create adjustable headers object
     */
    public static function create(string $component = self::DEFAULT_COMPONENT): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $headers = new self([
            self::LOG_ID => Uuid::uuid4()->toString(),
            self::LOG_SPAN => Uuid::uuid4()->toString(),
            self::LOG_PARENT_ID => '',
            self::LOG_DESCRIPTION => '',
            self::LOG_COMPONENT => $component,
            self::USER_LOG_KEY => '__MISSING_USER_HEADER__',
        ]);

        foreach ($headers->data as $neededHeader => $default) {
            if ($neededHeader === self::LOG_SPAN) {
                continue;
            }

            $existingValue = self::getHeaderValue($neededHeader);
            if ($existingValue !== null) {
                $headers->data[$neededHeader] = $existingValue;
            }
        }

        return self::$instance = $headers;
    }

    private static function getHeaderValue(string $header): ?string
    {
        return $_SERVER[self::getServerHeaderName($header)] ?? null;
    }

    private static function getServerHeaderName(string $header): string
    {
        return 'HTTP_' . \strtoupper(\str_replace('-', '_', $header));
    }

    public static function setServerHeader(string $header, string $value): void
    {
        $_SERVER[self::getServerHeaderName($header)] = $value;
    }

    public function setUser(UserInterface $user): void
    {
        $this->data[self::USER_LOG_ID_KEY] = $user->getId();
        $this->data[self::USER_LOG_KEY] = $user->getUsername();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): string
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->data);
    }

    public function set(string $key, string $value): void
    {
        $this->data[$key] = $value;
    }

    public function get(string $key, ?string $default = null): string
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function replace(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function all(): array
    {
        return $this->data;
    }

    public function keys(): array
    {
        return \array_keys($this->data);
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->data);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    public function clear(): void
    {
        static::$instance = null;
        $this->data = [];
    }

    public function fetchXLogHeadersForNextCall(): array
    {
        $headers = [];

        foreach ($this as $headerKey => $headerValue) {
            if (!$this->isXLogHeader($headerKey) || $headerKey === self::LOG_PARENT_ID) {
                continue;
            }

            if ($headerKey === self::LOG_SPAN) {
                $headerKey = self::LOG_PARENT_ID;
            }

            $headers[$headerKey] = $headerValue;
        }

        return $headers;
    }

    public function saveXLogHeadersFromInput(array $headers): void
    {
        foreach ($headers as $headerKey => $headerValue) {
            if (!$this->isXLogHeader($headerKey) || $headerKey === self::LOG_SPAN) {
                continue;
            }

            $this->set($headerKey, $headerValue);
        }
    }

    private function isXLogHeader(string $headerKey): bool
    {
        $prefix = 'X-LOG-';

        return \substr($headerKey, 0, \strlen($prefix)) === $prefix;
    }
}
