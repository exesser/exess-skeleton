<?php declare(strict_types=1);
namespace Helper;

use Helper\Constraint\ArraySubset;

class AssertHelper extends \Codeception\Module
{
    /**
     * Asserts two arrays of data are identical.
     */
    public function assertArrayEqual(array $expected, array $result): void
    {
        $this->compareArray($expected, $result);
        $this->compareArray($result, $expected);
    }

    /**
     * Do the actual comparing.
     *
     * @param string $path The path root name, for debugging purposes.
     */
    private function compareArray(array $expected, array $result, string $path = 'root'): void
    {
        foreach ($expected as $name => $value) {
            $this->assertTrue(\array_key_exists($name, $result), "Expected [$path]::$name to exist");

            if (\is_array($value)) {
                $this->assertTrue(\is_array($result[$name]));
                $this->compareArray($value, $result[$name], "$path.$name");
            } else {
                $this->assertEquals(
                    $value,
                    $result[$name],
                    "Expected that [$path]::$name => $value === {$result[$name]}"
                );
            }
        }
    }

    public function assertJsonEquals(string $expected, string $actual): void
    {
        $this->assertArrayEqual(\json_decode($expected, true), \json_decode($actual, true));
    }

    public function assertValidGuid(string $guid): void
    {
        $this->assertRegExp(
            '~^[a-f\d]{8}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{12}$~',
            $guid
        );
    }

    // @codingStandardsIgnoreStart
    /**
     * @param array  $subset
     * @param array  $array
     * @param bool   $strict
     * @param string $message
    */
    public function assertArraySubset($subset, $array, $strict = false, $message = ''): void
    {
        // @codingStandardsIgnoreEnd
        if (!(\is_array($subset) || $subset instanceof \ArrayAccess)) {
            throw new \InvalidArgumentException(
                'array or ArrayAccess',
                1
            );
        }

        if (!(\is_array($array) || $array instanceof \ArrayAccess)) {
            throw new \InvalidArgumentException(
                'array or ArrayAccess',
                1
            );
        }

        $constraint = new ArraySubset($subset, $strict);

        $this->assertThat($array, $constraint, $message);
    }

    public function assertAlmostNow(\DateTimeInterface $value): void
    {
        $this->assertEqualsWithDelta(
            new \DateTime('now'),
            $value,
            // Allows up to 10 second difference
            10
        );
    }
}
