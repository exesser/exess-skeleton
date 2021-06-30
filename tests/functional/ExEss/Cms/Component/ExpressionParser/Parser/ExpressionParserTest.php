<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\Component\ExpressionParser\Parser;

use ExEss\Bundle\CmsBundle\Doctrine\Type\FlowType;
use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\Expression;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\ExpressionParser;
use ExEss\Bundle\CmsBundle\Component\ExpressionParser\Parser\ExpressionParserOptions;
use Helper\Testcase\FunctionalTestCase;

class ExpressionParserTest extends FunctionalTestCase
{
    public function testSameExpressionWithOtherOptions(): void
    {
        /** @var ExpressionParser $parser */
        $parser = $this->tester->grabService(ExpressionParser::class);

        $raw = FlowType::DASHBOARD;

        /** @var Flow $flow */
        $flow = $this->tester->grabEntityFromRepository(
            Flow::class,
            ['id' => $this->tester->generateFlow(['type_c' => $raw])]
        );

        $label = FlowType::getValues()[$raw];

        $options = new ExpressionParserOptions($flow);

        // act & assert with option set 1
        // @todo re-enable
        // $expression = new Expression('type');
        // $parser->parse($expression, $options->setReplaceEnumValueWithLabel(true));
        // $this->tester->assertSame($label, $expression->getReplacement());

        // act & assert with option set 2
        $expression = new Expression('type');
        $parser->parse($expression, $options->setReplaceEnumValueWithLabel(false));
        $this->tester->assertSame($raw, $expression->getReplacement());

        // act & assert again with option set 1
        // @todo re-enable
        // $expression = new Expression('type');
        // $parser->parse($expression, $options->setReplaceEnumValueWithLabel(true));
        // $this->tester->assertSame($label, $expression->getReplacement());

        // act & assert again with option set 2
        $expression = new Expression('type');
        $parser->parse($expression, $options->setReplaceEnumValueWithLabel(false));
        $this->tester->assertSame($raw, $expression->getReplacement());
    }

    public function testClassMethodAndFieldParse(): void
    {
        /** @var ExpressionParser $parser */
        $parser = $this->tester->grabService(ExpressionParser::class);

        $runtimeClass = new class() {
            public string $someField = 'someFieldValue';

            public function getSomeMethod(): string
            {
                return 'SomeMethodValue';
            }

            public function isSomeOtherMethod(): string
            {
                return 'SomeOtherMethodValue';
            }
        };

        $options = new ExpressionParserOptions($runtimeClass);

        $expression = new Expression('someMethod');
        $parser->parse($expression, $options->setReplaceEnumValueWithLabel(true));
        $this->tester->assertSame('SomeMethodValue', $expression->getReplacement());

        $expression = new Expression('someOtherMethod');
        $parser->parse($expression, $options->setReplaceEnumValueWithLabel(true));
        $this->tester->assertSame('SomeOtherMethodValue', $expression->getReplacement());

        $expression = new Expression('someField');
        $parser->parse($expression, $options->setReplaceEnumValueWithLabel(true));
        $this->tester->assertSame('someFieldValue', $expression->getReplacement());
    }
}
