<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Service;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessage;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\FlashMessages\FlashMessageContainer;
use ExEss\Bundle\CmsBundle\Helper\DataCleaner;
use Ramsey\Uuid\Uuid;
use ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses\DynamicListResponse;

class ListExportService
{
    /**
     * Cell lines.
     */
    private const CELL_LINES = ['line1', 'line2', 'line3'];

    /**
     * CSV delimiter
     */
    private const CSV_DELIMITER = ';';

    /**
     * Maximum size of the list
     */
    public const LIMIT_OF_LIST = 300;

    /**
     * Temp directory for CSV files
     */
    public const CSV_URI_PATH = 'download/csv';

    /**
     * Temp directory for CSV files
     */
    public const CSV_DIR = 'public/' . self::CSV_URI_PATH;

    private string $siteUrl;

    /**
     * @var resource[]
     */
    private array $resources = [];

    private FlashMessageContainer $flashMessageContainer;

    public function __construct(string $siteUrl, FlashMessageContainer $flashMessageContainer)
    {
        $this->siteUrl = $siteUrl;
        $this->flashMessageContainer = $flashMessageContainer;
    }

    public function export(DynamicListResponse $list): string
    {
        $this->writeHeaders($list);
        $this->writeLines($list);

        $this->flashMessageContainer->addFlashMessage(
            new FlashMessage(
                "{$list->pagination->total} lines were exported to '$list->fileName'",
                FlashMessage::TYPE_SUCCESS
            )
        );

        return $this->getCsvUrl($list);
    }

    private function getCsvUrl(DynamicListResponse $list): string
    {
        if (null === $list->fileName) {
            $this->getCSVFileName($list);
        }

        return \rtrim($this->siteUrl, '/') . '/' . \str_replace(self::CSV_DIR, self::CSV_URI_PATH, $list->fileName);
    }

    private function getCSVFileName(DynamicListResponse $list): string
    {
        if ($list->fileName) {
            return $list->fileName;
        }

        $fileName = $this->getFileName($list->settings->getSlug());
        $list->fileName = $fileName;

        return $fileName;
    }

    /**
     * Writes the headers to the CSV file
     */
    private function writeHeaders(DynamicListResponse $list): void
    {
        $csv = $this->openFile($list);
        $headers = [];

        foreach ($list->headers as $header) {
            if ($header->cellType === 'list_icon_text_cell') {
                $headers[] = $header->label;
                continue;
            }

            foreach (self::CELL_LINES as $lineNo) {
                if ($header->cellLines->{$lineNo} ?? false === true) {
                    $headers[] = $header->cellLines->{$lineNo . 'CsvHeader'} ?? '' ?: $header->label;
                }
            }
        }

        if (!empty($headers)) {
            $this->writeLine($csv, DataCleaner::decodeData($headers));
        }
    }

    /**
     * Writes rows present on the list to the CSV file
     */
    private function writeLines(DynamicListResponse $list): void
    {
        try {
            $csv = $this->getResource($list);
        } catch (\RuntimeException $e) {
            $csv = $this->openFile($list, 'a');
        }

        foreach ($list->rows as $row) {
            $values = [];

            foreach ($row->cells as $key => $cell) {
                if ($cell->type === 'list_icon_text_cell') {
                    $values[] = DataCleaner::decodeData($cell->options->text);
                } else {
                    foreach (self::CELL_LINES as $lineNo) {
                        if (($cell->cellLines->{$lineNo} ?? false) === true) {
                            $value = $cell->options->{$lineNo} ?? null;

                            if (\is_bool($value)) {
                                $values[] = \var_export($value, true);
                            } else {
                                $values[] = DataCleaner::decodeData($value);
                            }
                        }
                    }
                }
            }

            $this->writeLine($csv, $values);
        }
    }

    /**
     * @return resource
     *
     * @throws \RuntimeException When resource was not found.
     */
    private function getResource(DynamicListResponse $list)
    {
        $resource = $this->resources[$list->fileName] ?? null;

        if (null === $resource) {
            throw new \RuntimeException("No resource for file '{$list->fileName}' was found");
        }

        return $resource;
    }

    private function getFileName(string $moduleName): string
    {
        if (!\file_exists(self::CSV_DIR)) {
            \mkdir(self::CSV_DIR, 0755, true);
        }

        return \sprintf(
            '%s/%s_%s_%s.csv',
            self::CSV_DIR,
            $moduleName,
            (new \DateTime())->format('YmdHis'),
            Uuid::uuid4()->toString()
        );
    }

    /**
     * Writes a line to the CSV
     *
     * @param resource $csv
     *
     * @throws \RuntimeException When file failed to write.
     */
    private function writeLine($csv, array $row): void
    {
        if (false === \fputcsv($csv, $row, self::CSV_DELIMITER)) {
            throw new \RuntimeException('Failed to write line to CSV');
        }
    }

    /**
     * @return bool|resource
     */
    private function openFile(DynamicListResponse $list, string $mode = 'wb')
    {
        if (empty($list->fileName)) {
            $this->getCSVFileName($list);
        }

        $csv = \fopen($list->fileName, $mode);

        if (false === \flock($csv, \LOCK_EX)) {
            throw new \RuntimeException("Could not acquire exclusive lock on the file '{$list->fileName}'");
        }

        $this->resources[$list->fileName] = $csv;

        return $csv;
    }

    /**
     * Close all orphan resources, if there are any.
     */
    public function __destruct()
    {
        foreach ($this->resources as $resource) {
            \fflush($resource);
            \flock($resource, \LOCK_UN);
        }
    }
}
