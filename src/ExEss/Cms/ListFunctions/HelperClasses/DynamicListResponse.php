<?php
namespace ExEss\Cms\ListFunctions\HelperClasses;

class DynamicListResponse
{
    public DynamicListSettings $settings;

    /**
     * @var DynamicListTopBar|bool
     */
    public $topBar;

    public array $headers;

    public array $rows;

    /**
     * The CSV File name when present
     */
    public ?string $fileName;

    public DynamicListPagination $pagination;

    public bool $async = false;

    public array $postedData = [];

    public function __construct(?string $filename = null)
    {
        $this->settings = new DynamicListSettings();
        $this->topBar = new DynamicListTopBar();
        $this->headers = [];
        $this->rows = [];
        $this->pagination = new DynamicListPagination();
        $this->fileName = $filename;
    }
}
