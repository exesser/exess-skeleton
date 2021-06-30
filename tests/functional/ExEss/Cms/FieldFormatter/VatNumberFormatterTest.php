<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FieldFormatter;

use ExEss\Bundle\CmsBundle\FieldFormatter\VatNumberFormatter;
use Helper\Testcase\FunctionalTestCase;

class VatNumberFormatterTest extends FunctionalTestCase
{
    private VatNumberFormatter $formatter;

    public function _before(): void
    {
        $this->formatter = $this->tester->grabService(VatNumberFormatter::class);
    }

    public function testVatNumberFormatting(): void
    {
        $expectedValue = 'BE0909090909';
        $expectedValueNl = 'NL999999999B99';

        //assertions
        $this->tester->assertEquals($expectedValue, $this->formatter->format($expectedValue));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('be0909090909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('BE 0909090909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('BE 0909.090.909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0909.090.909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('909.090.909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0909 090 909'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0909090909'));
        $this->tester->assertEquals($expectedValueNl, $this->formatter->format($expectedValueNl));
        $this->tester->assertEquals($expectedValueNl, $this->formatter->format('NL 9999.9999.9B99'));
        $this->tester->assertEquals($expectedValueNl, $this->formatter->format('NL 9999 9999 9B99'));
        $this->tester->assertEquals($expectedValueNl, $this->formatter->format('nl 9999 9999 9B99'));

        // Wrong values
        $this->tester->assertEquals('0123123', $this->formatter->format('0123123'));
    }

    public function testVatNumberValidation(): void
    {
        //assertions
        $this->tester->assertEquals(true, $this->formatter->isValid('BE0670861601'));
        $this->tester->assertEquals(true, $this->formatter->isValid('be0670861601'));
        $this->tester->assertEquals(false, $this->formatter->isValid('BE0670861602'));
        $this->tester->assertEquals(false, $this->formatter->isValid('NL99 999 9999 B99'));
        $this->tester->assertEquals(true, $this->formatter->isValid('NL999999999B99'));
        $this->tester->assertEquals(true, $this->formatter->isValid('nl999999999B99'));
        $this->tester->assertEquals(false, $this->formatter->isValid('0123123'));
    }
}
