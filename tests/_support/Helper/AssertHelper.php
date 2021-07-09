<?php declare(strict_types=1);

namespace Helper;

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
}
