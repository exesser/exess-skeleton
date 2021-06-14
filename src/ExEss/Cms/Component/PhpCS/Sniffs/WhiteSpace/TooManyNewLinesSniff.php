<?php declare(strict_types=1);

namespace ExEss\Cms\Component\PhpCS\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TooManyNewLinesSniff implements Sniff
{
    private const DISALLOWED_CONSECUTIVE_NEWLINES = 2;

    /**
     * @inheritDoc
     */
    public function register(): array
    {
        return [\T_OPEN_TAG];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $count = 0;
        $foundLinePtr = 0;
        $endOfFilePos = $phpcsFile->numTokens - 1;
        while ($stackPtr !== false) {
            $stackPtr = $phpcsFile->findNext([\T_WHITESPACE], ($stackPtr + 1));
            if ($stackPtr === false || $stackPtr === $endOfFilePos) {
                // at the end of the file we need 2 newlines
                $maxCount = self::DISALLOWED_CONSECUTIVE_NEWLINES + 1;
            } else {
                $maxCount = self::DISALLOWED_CONSECUTIVE_NEWLINES;
            }

            if (\trim($tokens[$stackPtr]['content'], ' ') === $phpcsFile->eolChar) {
                if (($tokens[$stackPtr]['line'] - $tokens[$foundLinePtr]['line']) === 1) {
                    // check tokens in between for something else than space (not eol)
                    $actualCodeFound = false;
                    for ($x = $stackPtr, $min = $foundLinePtr; $x > $min; $x--) {
                        if ($tokens[$x]['type'] !== 'T_WHITESPACE') {
                            $actualCodeFound = true;
                            break;
                        }
                    }
                    // if consecutive newlines are found (even with space or tabs in between)
                    // increase the count
                    if (!$actualCodeFound) {
                        $count++;
                    }
                }
                $foundLinePtr = $stackPtr;
            } else {
                $count = 0;
            }
            if ($count >= $maxCount) {
                $error = 'Too much consecutive new lines found';
                $fix = $phpcsFile->addFixableWarning($error, $stackPtr, 'Found');
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($stackPtr, null);
                }
            }
        }
    }
}
