<?php

namespace ExEss\Cms\Api\V8_Custom\Service\FlashMessages;

class FlashMessage implements \JsonSerializable
{
    public const TYPE_ERROR = 'ERROR';
    public const TYPE_WARNING = 'WARNING';
    public const TYPE_SUCCESS = 'SUCCESS';
    public const TYPE_INFORMATION = 'INFORMATION';
    public const GROUP_DEFAULT = '';
    public const GROUP_SERVICEMIX = 'servicemix';

    private string $type;

    private string $text;

    private string $group;

    private int $count = 1;

    /**
     * Get possible types
     *
     * @return array
     */
    public function possibleTypes(): array
    {
        return [
            self::TYPE_ERROR,
            self::TYPE_WARNING,
            self::TYPE_SUCCESS,
            self::TYPE_INFORMATION
        ];
    }

    public function __construct(string $text, string $type = self::TYPE_ERROR, string $group = self::GROUP_DEFAULT)
    {
        $type = \in_array($type, $this->possibleTypes(), true) ? $type : self::TYPE_ERROR;

        $this->type = $type;
        $this->text = $text;
        $this->group = $group;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
            'group' => $this->group
        ];
    }

    public function equals(FlashMessage $flashMessage): bool
    {
        return
            $this->text === $flashMessage->text
            && $this->type === $flashMessage->type
            && $this->group === $flashMessage->group;
    }

    public function addCount(int $i = 1): void
    {
        $this->count += $i;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function isError(): bool
    {
        return $this->type === self::TYPE_ERROR;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
