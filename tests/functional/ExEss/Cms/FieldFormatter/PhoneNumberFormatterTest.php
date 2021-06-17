<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Cms\FieldFormatter;

use ExEss\Cms\FieldFormatter\PhoneNumberFormatter;
use Helper\Testcase\FunctionalTestCase;

class PhoneNumberFormatterTest extends FunctionalTestCase
{
    private PhoneNumberFormatter $formatter;

    public function _before(): void
    {
        $this->formatter = $this->tester->grabService(PhoneNumberFormatter::class);
    }

    public function testPhoneNumberFormatting(): void
    {
        $expectedValue = '+32 495 50 40 30';

        //assertions
        $this->tester->assertEquals($expectedValue, $this->formatter->format($expectedValue));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('+32 495 504 030'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('+32495504030'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0495 504 030'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0495504030'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0495.504.030'));
        $this->tester->assertEquals($expectedValue, $this->formatter->format('0032 495.504.030'));

        // Incorrect values
        $this->tester->assertEquals('0032', $this->formatter->format('0032'));
        $this->tester->assertEquals('dklfjsdlfkjsdflkjsfd', $this->formatter->format('dklfjsdlfkjsdflkjsfd'));
    }

    public function testPhoneNumberValidation(): void
    {
        $expectedValue = '+32 495 50 40 30';

        //assertions
        $this->tester->assertEquals(true, $this->formatter->isValid($expectedValue));
        $this->tester->assertEquals(false, $this->formatter->isValid('+32495504030'));
        $this->tester->assertEquals(false, $this->formatter->isValid('0495 504 030'));
        $this->tester->assertEquals(false, $this->formatter->isValid('0495 504 030'));
        $this->tester->assertEquals(false, $this->formatter->isValid('0495504030'));
        $this->tester->assertEquals(false, $this->formatter->isValid('0032'));
        $this->tester->assertEquals(false, $this->formatter->isValid('dklfjsdlfkjsdflkjsfd'));
    }
}
