<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Service;

use ExEss\Cms\ListFunctions\HelperClasses\DynamicListHeader;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListResponse;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListRow;
use ExEss\Cms\ListFunctions\HelperClasses\DynamicListRowCell;
use ExEss\Cms\Service\ListExportService;
use Helper\Testcase\FunctionalTestCase;

class ListExportServiceTest extends FunctionalTestCase
{
    private ListExportService $listExportService;

    public function _before(): void
    {
        $this->listExportService = $this->tester->grabService(ListExportService::class);
    }

    public function testGetCSVLink(): void
    {
        // Given
        $dynamicList = $this->mockDynamicList();

        // When
        $result = $this->listExportService->export($dynamicList);

        // Then
        $this->tester->assertStringNotContainsString(ListExportService::CSV_DIR, $result);
        $this->tester->seeFileFound(\basename($dynamicList->fileName), ListExportService::CSV_DIR);
        $this->tester->assertFileExists($dynamicList->fileName);
        $this->tester->assertEquals(
            \file_get_contents(__DIR__ . '/resources/ListExportService_expected.csv'),
            \file_get_contents($dynamicList->fileName)
        );

        // Cleanup
        $this->tester->deleteFile($dynamicList->fileName);
    }

    private function mockDynamicList(): DynamicListResponse
    {
        $response = new DynamicListResponse();
        $response->settings->title = 'Account';

        $header1 = new DynamicListHeader();
        $header1->cellType = 'list_icon_text_cell';
        $header1->label = 'Company';

        $header2 = new DynamicListHeader();
        $header2->cellType = 'list_simple_two_liner_cell';
        $header2->cellLines->line1 = true;
        $header2->cellLines->line2 = true;
        $header2->cellLines->line3 = false;

        $header2->cellLines->line1CsvHeader = 'FirstName';
        $header2->cellLines->line2CsvHeader = '';
        $header2->cellLines->line3CsvHeader = 'BLA';
        $header2->label = 'Name';

        $header3 = new DynamicListHeader();
        $header3->cellType = 'list_simple_two_liner_cell';
        $header3->cellLines->line1 = true;
        $header3->cellLines->line2 = false;
        $header3->cellLines->line3 = false;

        $header3->cellLines->line1CsvHeader = 'Valid';
        $header3->cellLines->line2CsvHeader = '';
        $header3->cellLines->line3CsvHeader = '';

        $response->headers = [$header1, $header2, $header3];

        $row1 = new DynamicListRow();
        $row1cell1 = new DynamicListRowCell();
        $row1cell1->type = 'list_icon_text_cell';
        $row1cell1->options = (object) ["text" => "Hooniçan"];

        $row1cell2 = new DynamicListRowCell();
        $row1cell2->type = 'list_simple_two_liner_cell';
        $row1cell2->cellLines = (object) ["line1" => true, "line2" => true, "line3" => false];
        $row1cell2->options = (object) ["line1" => "Kén", "line2" => "Blôck", "line3" => ""];

        $row1cell3 = new DynamicListRowCell();
        $row1cell3->type = 'list_simple_two_liner_cell';
        $row1cell3->cellLines = (object) ["line1" => true, "line2" => false, "line3" => false];
        $row1cell3->options = (object) ["line1" => true, "line2" => "", "line3" => ""];

        $row1->cells = [$row1cell1, $row1cell2, $row1cell3];

        $row2 = new DynamicListRow();
        $row2cell1 = new DynamicListRowCell();
        $row2cell1->type = 'list_icon_text_cell';
        $row2cell1->options = (object) ["text" => "ABL"];

        $row2cell2 = new DynamicListRowCell();
        $row2cell2->type = 'list_simple_two_liner_cell';
        $row2cell2->cellLines = (object) ["line1" => true, "line2" => true, "line3" => false];
        $row2cell2->options = (object) ["line1" => "Andreas", "line2" => "Bakkerud", "line3" => ""];

        $row2cell3 = new DynamicListRowCell();
        $row2cell3->type = 'list_simple_two_liner_cell';
        $row2cell3->cellLines = (object) ["line1" => true, "line2" => false, "line3" => false];
        $row2cell3->options = (object) ["line1" => false, "line2" => "", "line3" => ""];

        $row2->cells = [$row2cell1, $row2cell2, $row2cell3];

        $response->rows = [$row1, $row2];
        $response->pagination->total = \count($response->rows);

        return $response;
    }
}
