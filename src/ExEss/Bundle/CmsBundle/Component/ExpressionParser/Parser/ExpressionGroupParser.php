<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser;

class ExpressionGroupParser
{
    private ExpressionParser $expressionParser;

    public function __construct(
        ExpressionParser $expressionParser
    ) {
        $this->expressionParser = $expressionParser;
    }

    /**
     * @throws \RuntimeException In case of a parsing error.
     */
    public function parse(
        ExpressionGroup $expressionGroup,
        ExpressionParserOptions $parserOptions,
        ?PathResolverOptions $resolverOptions = null
    ): void {
        if ($parserOptions->getBaseEntity() === false) {
            throw new \RuntimeException('The base entity should be an object or null');
        }

        // parse each expression
        /** @var \ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Expression $expression */
        foreach ($expressionGroup->getExpressions() as $expression) {
            $this->expressionParser->parse($expression, $parserOptions, $resolverOptions);

            if (!$expression->isParsed()) {
                throw new \RuntimeException(\sprintf('Expression "%s" failed to parse', (string) $expression));
            }
        }
    }
}
