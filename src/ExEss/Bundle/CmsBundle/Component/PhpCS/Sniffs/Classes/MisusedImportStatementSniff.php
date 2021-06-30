<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\PhpCS\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;

class MisusedImportStatementSniff implements Sniff
{
    public function register(): array
    {
        return [\T_USE];
    }

    /**
     * @inheritDoc
     */
    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Only check use statements in the global scope.
        if (!empty($tokens[$stackPtr]['conditions'])) {
            return;
        }

        // Seek to the end of the statement and get the string before the semi colon.
        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        if ($tokens[$semiColon]['code'] !== \T_SEMICOLON) {
            return;
        }

        $startClassPtr = $phpcsFile->findNext(\T_STRING, $stackPtr + 1);

        $classPtr = $phpcsFile->findPrevious(
            \PHP_CodeSniffer\Util\Tokens::$emptyTokens,
            $semiColon - 1,
            null,
            true
        );

        if ($tokens[$classPtr]['code'] !== \T_STRING) {
            return;
        }

        $nsSep = $phpcsFile->findNext(\T_NS_SEPARATOR, $classPtr + 1);

        while ($nsSep !== false) {
            $beforeUsage = $phpcsFile->findPrevious(
                [\T_STRING, \T_NS_SEPARATOR, \T_WHITESPACE],
                $nsSep - 1,
                null,
                true
            );

            if ($tokens[$beforeUsage]["code"] === \T_USE) {
                // If inside an import, skip statement and continue
                $nsSep = $phpcsFile->findNext(\T_NS_SEPARATOR, $phpcsFile->findEndOfStatement($nsSep) + 1);
                continue;
            }

            $duplicate = true;

            // Check for NS not starting with a root "\"
            if (
                $tokens[$nsSep - 1]["code"] === \T_STRING
                && $tokens[$nsSep - 1]["content"] === $tokens[$startClassPtr]["content"]
            ) {
                $beginAt = -1;
            } else {
                $beginAt = 1;
            }

            for ($offset = 0; $offset <= $classPtr - $startClassPtr; ++$offset) {
                if (
                    $tokens[$nsSep + $offset + $beginAt]["code"] !== $tokens[$startClassPtr + $offset]["code"]
                    || $tokens[$nsSep + $offset + $beginAt]["content"] !== $tokens[$startClassPtr + $offset]["content"]
                ) {
                    $duplicate = false;
                    // Skip some tokens
                    $nsSep = $phpcsFile->findNext([\T_NS_SEPARATOR, \T_STRING], $nsSep + 1, null, true);
                    break;
                }
            }

            if ($duplicate
                && $phpcsFile->addFixableWarning(
                    'Reuse imported "' . $tokens[$classPtr]['content'] . '" symbol',
                    $stackPtr,
                    'MisusedUse'
                )
            ) {
                $phpcsFile->fixer->beginChangeset();

                if ($beginAt === 1) {
                    $offset = 0;
                    $end = $classPtr - $startClassPtr;
                } else {
                    $offset = -1;
                    $end = $classPtr - $startClassPtr - 2;
                }

                while ($offset <= $end) {
                    $phpcsFile->fixer->replaceToken($nsSep + $offset, '');
                    ++$offset;
                }

                $phpcsFile->fixer->endChangeset();
            }

            $nsSep = $phpcsFile->findNext(\T_NS_SEPARATOR, $nsSep + 1);
        }
    }
}
