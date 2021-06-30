<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses;

class DynamicListResponse implements \JsonSerializable
{
    public DynamicListSettings $settings;

    /**
     * @var DynamicListTopBar|bool
     */
    public $topBar;

    public array $headers = [];

    public array $rows = [];

    /**
     * The CSV File name when present
     */
    public ?string $fileName;

    public DynamicListPagination $pagination;

    public bool $async = false;

    public array $postedData = [];

    public function __construct(?string $filename = null)
    {
        $this->fileName = $filename;
        $this->settings = new DynamicListSettings();
        $this->topBar = new DynamicListTopBar();
        $this->pagination = new DynamicListPagination();
    }

    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
