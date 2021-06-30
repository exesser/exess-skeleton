<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser;

use Doctrine\Common\Collections\Collection;
use ExEss\Bundle\CmsBundle\Collection\ObjectCollection;
use ExEss\Bundle\CmsBundle\Entity\ListCellLink;
use ExEss\Bundle\CmsBundle\ListFunctions\HelperClasses\DynamicListTopBar;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Compiler\ExternalObjectCompiler;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Query\ConditionsParser;

class ExpressionGroup
{
    private string $original;

    /**
     * @var ObjectCollection|Expression[]
     */
    private $expressions;

    public function __construct(string $original)
    {
        $this->split($original);
    }

    /**
     * @return static
     */
    public static function createForCellsAndTopActions(
        Collection $cellLinks,
        bool $exportToCSV,
        ?DynamicListTopBar $topBar
    ): self {
        // always select the id and always select it as the first expression (parseListQuery depends on it)
        $select = [
            '%id%' => true,
        ];

        /** @var ListCellLink $cellLink */
        foreach ($cellLinks as $cellLink) {
            $myCell = $cellLink->getCell();
            if (!$myCell->isVisible($exportToCSV)) {
                continue;
            }

            if (!empty($myCell->getLine1())) {
                $select[$myCell->getLine1()] = true;
            }
            if (!empty($myCell->getLine2())) {
                $select[$myCell->getLine2()] = true;
            }
            if (!empty($myCell->getLine3())) {
                $select[$myCell->getLine3()] = true;
            }
            if (!empty($myCell->getLink())) {
                $select[$myCell->getLink()] = true;
            }
            if (!empty($params = $myCell->getParams())) {
                $select[\json_encode($params)] = true;
            }
            if (!empty($myCell->getActionKey())) {
                $select[$myCell->getActionKey()] = true;
            }
            if (!empty($myCell->getIcon())) {
                $select[$myCell->getIcon()] = true;
            }
        }

        if ($topBar instanceof DynamicListTopBar) {
            foreach ($topBar->buttons ?? [] as $button) {
                if (isset($button->action['extraParamsFromRow'])) {
                    $select[\json_encode($button->action['extraParamsFromRow'])] = true;
                }
            }
        }

        // for external objects, the conditions need to be parsed as they should be fetched as well
        $extra = [];
        foreach ($select as $key => $selected) {
            $matches = [];
            \preg_match_all(ExternalObjectCompiler::REGEX_EXTERNAL_HANDLER, $key, $matches);
            foreach ($matches[2] as $argumentString) {
                $conditions = ConditionsParser::parse($argumentString);
                if (!empty($where = $conditions->getWhere())) {
                    $arguments = ExternalObjectCompiler::getArgumentsFrom($where[0]);
                    foreach ($arguments as $argument) {
                        if (\is_string($argument)) {
                            $extra['%'.$argument.'%'] = true;
                        }
                    }
                }
            }
        }

        return new self(\implode(' ', \array_merge(\array_keys($select), \array_keys($extra))));
    }

    private function split(string $original): void
    {
        $this->original = $this->unquote($original);
        $this->expressions = new ObjectCollection(Expression::class);

        // split string to be parse into the individual string to parse
        \preg_match_all('/([\{]{0,1})%(.*?[^\\\\])%([\}]{0,1})/', $original, $matches);

        foreach ($matches[2] as $index => $match) {
            if (!empty($matches[1][$index]) && !empty($matches[3][$index])) {
                // expression was wrapped with { and }
                continue;
            }
            $this->expressions[] = new Expression($this->unquote($match));
        }
    }

    private function unquote(string $line): string
    {
        return \str_replace('\%', '%', $line);
    }

    /**
     * @return ObjectCollection|Expression[]
     */
    public function getExpressions(): ObjectCollection
    {
        return $this->expressions;
    }

    public function isParsed(): bool
    {
        /** @var Expression $expression */
        foreach ($this->expressions as $expression) {
            if (!$expression->isParsed()) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        return $this->original;
    }
}
