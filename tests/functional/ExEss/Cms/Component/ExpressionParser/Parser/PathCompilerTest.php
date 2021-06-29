<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser;

use ExEss\Cms\Entity\ListCell;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathCompiler;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\PathResolverOptions;
use Helper\Testcase\FunctionalTestCase;

class PathCompilerTest extends FunctionalTestCase
{
    private PathCompiler $pathCompiler;

    public function _before(): void
    {
        $this->pathCompiler = $this->tester->grabService(PathCompiler::class);
    }

    public function testSamePathMultipleTimes(): void
    {
        // Given
        $listId = $this->tester->generateDynamicList();
        $this->tester->generateListLinkCell($listId, [
            'cell_id' => $cellId = $this->tester->generateListCell(),
        ]);
        $this->tester->generateListLinkCell(null, [
            'cell_id' => $orphanCellId = $this->tester->generateListCell(),
        ]);

        /** @var ListCell $cell */
        $cell = $this->tester->grabEntityFromRepository(
            ListCell::class,
            ['id' => $cellId]
        );
        /** @var ListCell $orphanCell */
        $orphanCell = $this->tester->grabEntityFromRepository(
            ListCell::class,
            ['id' => $orphanCellId]
        );

        $path = 'cellLinks|list';

        // When
        $options = (new PathResolverOptions())
            ->setCacheable(false)
        ;

        // Then
        $this->tester->assertEquals(
            $this->pathCompiler->compile($cell, $path, $options),
            $this->pathCompiler->compile($orphanCell, $path, $options)
        );

        // When
        $options = (new PathResolverOptions())
            ->setCacheable(true)
        ;
        $this->pathCompiler->compile($cell, $path, $options);

        // Then
        $this->tester->assertEquals(
            $this->pathCompiler->compile($cell, $path, $options),
            $this->pathCompiler->compile($orphanCell, $path, $options)
        );
    }
}
