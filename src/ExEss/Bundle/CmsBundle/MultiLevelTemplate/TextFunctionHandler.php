<?php

namespace ExEss\Bundle\CmsBundle\MultiLevelTemplate;

use ExEss\Bundle\CmsBundle\Dictionary\Format;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Translation\Translator;

class TextFunctionHandler
{
    private const ENCLOSURE_START = '{';
    private const ENCLOSURE_END = '}';

    private const FIRST_OF_NEXT_MONTH = 'firstofnextmonth';

    private int $numberOfDecimals = 8;

    private Logger $logger;

    private Translator $translator;

    private ExpressionLanguage $expression;

    private array $functions = [
        'round',
        'multiply',
        'divide',
        'sum',
        'subtract',
        'date',
        'dateadd',
        'datesub',
        'datediff',
        'durationMonths',
        'decimal',
        'upper',
        'lower',
        'translate',
        'translate!',
        'ceil',
        'floor',
        'if',
        'replace',
        'max',
        'min',
        self::FIRST_OF_NEXT_MONTH
    ];

    public function __construct(Logger $logger, Translator $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->expression = new ExpressionLanguage();
    }

    /**
     * @return mixed
     */
    public function resolveFunctions(
        ?string $text,
        ?string $language = null
    ) {
        if (empty($text)) {
            return $text;
        }
        while ($match = $this->getNextFunctionMatch($text)) {
            [$text, $matched] = $this->handleMatch($text, $match, $language);
        }

        return $text;
    }

    /**
     * @throws \Exception When an illegal function operator is provided (divby0, nonnumeric or incomplete).
     */
    protected function handleMatch(
        string $text,
        array $match,
        ?string $language = null
    ): array {
        $matchParameters = $match['parameters'];
        $matchReplacement = $match['replace_string'];
        $matchFunction = $match['function'];
        $result = '';

        while ($match = $this->getNextFunctionMatch($matchParameters)) {
            [$text, $matched] = $this->handleMatch($text, $match, $language);
            $matchParameters = \str_replace(
                $match['replace_string'],
                $matched,
                $matchParameters
            );

            $matchReplacement = \str_replace(
                $match['replace_string'],
                $matched,
                $matchReplacement
            );
        }

        $parameters = \explode(';', $matchParameters);

        if ($this->validateFunction($matchFunction, $parameters, $matchReplacement)) {
            switch ($matchFunction) {
                case 'round':
                    $precision = (int) $this->convertToFloat($parameters[1]);
                    $result = $this->applyDelimiters(
                        \round(
                            $this->convertToFloat($parameters[0]),
                            $precision
                        ),
                        $precision
                    );
                    break;
                case 'ceil':
                    $result = \ceil($parameters[0]);
                    break;
                case 'floor':
                    $result = \floor($parameters[0]);
                    break;
                case 'multiply':
                    $nbrOfDecimals = $parameters[2] ?? $this->numberOfDecimals;
                    $result = $this->applyDelimiters(
                        $this->convertToFloat($parameters[0]) *
                        $this->convertToFloat($parameters[1]),
                        $nbrOfDecimals
                    );
                    break;
                case 'divide':
                    $nbrOfDecimals = $parameters[2] ?? $this->numberOfDecimals;
                    $result = $this->applyDelimiters(
                        $this->convertToFloat($parameters[0]) /
                        $this->convertToFloat($parameters[1]),
                        $nbrOfDecimals
                    );
                    break;
                case 'sum':
                    $nbrOfDecimals = $parameters[2] ?? $this->numberOfDecimals;
                    $result = $this->applyDelimiters(
                        $this->convertToFloat($parameters[0]) +
                        $this->convertToFloat($parameters[1]),
                        $nbrOfDecimals
                    );
                    break;
                case 'subtract':
                    $nbrOfDecimals = $parameters[2] ?? $this->numberOfDecimals;
                    $result = $this->applyDelimiters(
                        $this->convertToFloat($parameters[0]) -
                        $this->convertToFloat($parameters[1]),
                        $nbrOfDecimals
                    );
                    break;
                case 'dateadd':
                    $dateTime = $this->getDateTimeObject($matchFunction, $parameters[0]);
                    if ($dateTime) {
                        $result = $dateTime->add(new \DateInterval($parameters[2]))->format($parameters[1]);
                    }
                    break;
                case 'datesub':
                    $dateTime = $this->getDateTimeObject($matchFunction, $parameters[0]);
                    if ($dateTime) {
                        $result = $dateTime->sub(new \DateInterval($parameters[2]))->format($parameters[1]);
                    }
                    break;
                case 'datediff':
                    $result = $this->getDateTimeObject($matchFunction, $parameters[0])
                        ->diff($this->getDateTimeObject($matchFunction, $parameters[1]))
                        ->format($parameters[2]);
                    break;
                case 'date':
                    if (!\is_string($parameters[0]) || empty($parameters[0])) {
                        break;
                    }
                    $dateTime = $this->getDateTimeObject($matchFunction, $parameters[0]);
                    if ($dateTime) {
                        if (!empty($parameters[2])) {
                            $dateTime = $dateTime->setTimezone(new \DateTimeZone($parameters[2]));
                            if (empty($dateTime)) {
                                throw new \InvalidArgumentException(
                                    $this->logError($matchFunction, $parameters[2] . ' is not a valid timezone')
                                );
                            }
                        }
                        $result = $dateTime->format($parameters[1]);
                    }
                    break;
                case 'decimal':
                    $decimal = $this->convertToFloat($parameters[0]);
                    $result = $this->applyDelimiters(
                        $decimal,
                        (int) $parameters[1],
                        $parameters[2],
                        $parameters[3]
                    );
                    break;
                case 'upper':
                    $result = \strtoupper($parameters[0]);
                    break;
                case 'lower':
                    $result = \strtolower($parameters[0]);
                    break;
                case 'translate':
                case 'translate!':
                    if (empty($language) && !empty($parameters[2])) {
                        $language = $parameters[2];
                    }
                    // When exclamation mark "!" is used make sure that translation exists otherwise return empty.
                    if ($matchFunction === 'translate!'
                        && !$this->translator->getCatalogue($language)->has($parameters[0], $parameters[1])) {
                        $result = '';
                        break;
                    }

                    $result = $this->translator->trans($parameters[0], [], $parameters[1], $language);
                    break;
                case self::FIRST_OF_NEXT_MONTH:
                    if (!\is_string($parameters[0]) || empty($parameters[0])) {
                        break;
                    }
                    $result = $this->getDateTimeObject($matchFunction, $parameters[0])
                        ->modify('first day of next month')
                        ->format($parameters[1] ?? Format::DB_DATE_FORMAT)
                    ;
                    break;
                case 'if':
                    try {
                        $result = $this->expression->evaluate($parameters[0]) ? $parameters[1] : $parameters[2] ?? '';
                    } catch (SyntaxError $e) {
                        $result = '';
                    }
                    break;
                case 'replace':
                    $result = \str_replace($parameters[0], $parameters[1], $parameters[2]);
                    break;
                case 'max':
                    $result = \max($parameters);
                    break;
                case 'min':
                    $result = \min($parameters);
                    break;
            }
        }

        return [
            $this->filterVariable(\str_replace($matchReplacement, $result, $text)),
            $result,
        ];
    }

    /**
     * Tries to convert the value to booleans, if not, return values as-is.
     *
     * @param mixed $variable
     * @return mixed|bool
     */
    private function filterVariable($variable)
    {
        if ($variable === 'true') {
            return true;
        }

        if ($variable === 'false') {
            return false;
        }

        return $variable;
    }

    private function getDateTimeObject(
        string $function,
        string $date
    ): ?\DateTimeImmutable {
        switch (\strtolower($date)) {
            case 'today':
            case 'now':
                $dateTime = new \DateTimeImmutable();
                break;
            default:
                if ($date[4] === '-') {
                    $format = '!' . Format::DB_DATE_FORMAT;
                } else {
                    $format = '!d-m-Y';
                }

                switch (\strlen($date)) {
                    case 16: // Date with hours and minutes (pricing date) 2000-01-01 10:10
                        $format .= ' H:i';
                        break;
                    case 19: // Full datetime in input string
                        $format .= ' H:i:s';
                        break;
                }

                $dateTime = \DateTimeImmutable::createFromFormat(
                    $format,
                    $date
                );

                if (!$dateTime) {
                    $dateTime = \date_create($date);
                }
        }

        if (!$dateTime) {
            throw new \InvalidArgumentException(
                $this->logError($function, $date . ' is not in a valid date string format')
            );
        }

        return $dateTime instanceof \DateTime ? \DateTimeImmutable::createFromMutable($dateTime) : $dateTime;
    }

    /**
     * @throws \Exception When an illegal function operator is provided (divby0, nonnumeric or incomplete).
     */
    private function validateFunction(
        string $function,
        array $parameters,
        string $functionText
    ): bool {
        switch ($function) {
            case 'round':
            case 'multiply':
            case 'sum':
            case 'subtract':
                return (
                    $this->validateNbrOfParameters($function, $functionText, $parameters, 2)
                    && $this->validateNumeric($function, $functionText, $parameters[0])
                    && $this->validateNumeric($function, $functionText, $parameters[1])
                );
            case 'ceil':
            case 'floor':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 1)
                    && $this->validateNumeric($function, $functionText, $parameters[0]);
            case 'divide':
                $validation = (
                    $this->validateNbrOfParameters($function, $functionText, $parameters, 2)
                    && $this->validateNumeric($function, $functionText, $parameters[0])
                    && $this->validateNumeric($function, $functionText, $parameters[1])
                );
                if ($validation && ((float)($parameters[1])) == 0) {
                    throw new \InvalidArgumentException(
                        $this->logError($function, 'Division by zero encountered in PDF generation: ' . $functionText)
                    );
                }
                return $validation;
            case 'date':
            case 'dateadd':
            case 'datediff':
            case 'durationMonths':
            case 'datesub':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 2);
            case 'decimal':
                return (
                    $this->validateNbrOfParameters($function, $functionText, $parameters, 4)
                    && $this->validateNumeric($function, $functionText, $parameters[0])
                    && $this->validateNumeric($function, $functionText, $parameters[1])
                );
            case 'upper':
            case 'lower':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 1);
            case 'translate':
            case 'translate!':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 2);
            case self::FIRST_OF_NEXT_MONTH:
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 1);
            case 'if':
                return \count($parameters) > 1;
            case 'replace':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 3);
            case 'max':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 2);
            case 'min':
                return $this->validateNbrOfParameters($function, $functionText, $parameters, 2);
            default:
                throw new \InvalidArgumentException(
                    $this->logError($function, 'Function ' . $functionText . ' not implemented')
                );
        }
    }

    /**
     * @throws \Exception When incorrect number of parameters is specified.
     */
    private function validateNbrOfParameters(
        string $function,
        string $functionText,
        array $parameters,
        int $nbrOfParameters
    ): bool {
        if (\count($parameters) >= $nbrOfParameters) {
            return true;
        }

        throw new \InvalidArgumentException(
            $this->logError(
                $function,
                'Function definition error encountered (template function not complete): ' . $functionText
            )
        );
    }

    /**
     * @throws \Exception When string provided is not numeric formatted.
     */
    private function validateNumeric(
        string $function,
        string $functionText,
        string $parameter
    ): bool {
        $parameter = \str_replace(',', '.', $parameter);

        // empty should be zero
        if (empty($parameter)) {
            return true;
        }

        //if the value is missing in the DB we get string null back from the parselist
        //which we do not want to show as error
        if ($parameter === 'null') {
            return false;
        }

        if (!\is_numeric($parameter)) {
            throw new \InvalidArgumentException(
                $this->logError(
                    $function,
                    'Function definition error encountered (Non numeric character specified): ' . $functionText
                )
            );
        }

        return true;
    }

    /**
     * @throws \InvalidArgumentException When the throw error flag is set to true.
     */
    private function logError(string $function, string $msg): string
    {
        $this->logger->critical($errorMsg = "TextFunction $function - $msg");

        return $errorMsg;
    }

    private function getNextFunctionMatch(string $text): ?array
    {
        $firstTagPos = 0;
        $firstFunction = null;

        // get first matching function tag:
        foreach ($this->functions as $function) {
            $pos = \strpos($text, self::ENCLOSURE_START . $function . self::ENCLOSURE_END);
            if ($pos !== false && (empty($firstFunction) || $pos < $firstTagPos)) {
                $firstTagPos = $pos;
                $firstFunction = $function;
            }
        }

        if ($firstFunction) {
            $startTag = self::ENCLOSURE_START . $firstFunction . self::ENCLOSURE_END;
            $endTag = self::ENCLOSURE_START . '/' . $firstFunction . self::ENCLOSURE_END;

            if ($functionCall = $this->getPartBetweenTags($startTag, $endTag, $text)) {
                return [
                    'function' => $firstFunction,
                    'parameters' => $functionCall,
                    'replace_string' => $startTag . $functionCall . $endTag,
                ];
            }
        }

        return null;
    }

    public function hasFunctions(string $text): bool
    {
        return null !== $this->getNextFunctionMatch($text);
    }

    /**
     * This method overrides the standard one in the Parser.php
     * The difference between the 2 methods is that the standard one does not allow for nesting of tags:
     * Example when looking for round tag in: {round}{sum}{round}10.123;2{/round};10{/sum}{/round}
     * In base method would return {sum}{round}10.123;2
     * In this method will return {sum}{round}10.123;2{/round};10{/sum}
     */
    protected function getPartBetweenTags(string $start, string $end, string $string): ?string
    {
        if (empty($start) || empty($end) || empty($string)) {
            return null;
        }

        $ini = \strpos($string, $start);
        if ($ini === false) {
            return null;
        }
        $ini += \strlen($start);
        $endPos = $ini;

        $keepSearching = true;
        while ($keepSearching) {
            $newPos = \strpos($string, $end, $endPos+1);
            if ($newPos === false) {
                $keepSearching = false;
            } else {
                $endPos = $newPos;
            }
            $field = \substr($string, $ini, $endPos - $ini);
            if (\substr_count($field, $start) === \substr_count($field, $end)) {
                $keepSearching = false;
            }
        }

        return $field;
    }

    private function applyDelimiters(
        float $floatValue,
        ?int $numberOfDecimals = null,
        ?string $decimalSeparator = null,
        ?string $thousandSeparator = null
    ): string {
        //Only if all format settings are given will we format the string
        if (isset($numberOfDecimals, $decimalSeparator, $thousandSeparator)) {
            return \number_format($floatValue, $numberOfDecimals, $decimalSeparator, $thousandSeparator);
        }

        //When no formatting options were specified, we return in standard float format, with rounding if needed
        if ($numberOfDecimals) {
            $floatValue = \number_format(\round($floatValue, $numberOfDecimals), $numberOfDecimals, '.', '');
        }
        return (string) $floatValue;
    }

    protected function convertToFloat(
        string $value
    ): float {
        return (float) $value;
    }
}
