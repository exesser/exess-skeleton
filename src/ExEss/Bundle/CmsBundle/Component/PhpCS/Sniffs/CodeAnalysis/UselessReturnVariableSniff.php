<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\PhpCS\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class UselessReturnVariableSniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register(): array
    {
        return [\T_RETURN];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $nextNonWS = $phpcsFile->findNext(\T_WHITESPACE, $stackPtr + 1, null, true);
        // Ensure we are in the case of returning a simple variable
        if ($tokens[$nextNonWS]['code'] !== \T_VARIABLE
            || $tokens[$phpcsFile->findNext(\T_WHITESPACE, $nextNonWS + 1, null, true)]['code'] !== \T_SEMICOLON
        ) {
            return;
        }

        $variableAssignment =
            $phpcsFile->findStartOfStatement($phpcsFile->findPrevious(\T_SEMICOLON, $stackPtr - 1) - 1);

        if (
            $tokens[$variableAssignment]['code'] === \T_VARIABLE
            && $tokens[$variableAssignment]['content'] === $tokens[$nextNonWS]['content']
            && $tokens[$variableAssignment]['level'] === $tokens[$nextNonWS]['level']
            && $tokens[$phpcsFile->findNext(\T_WHITESPACE, $variableAssignment + 1, null, true)]['code'] === \T_EQUAL
        ) {
            $level = $tokens[$variableAssignment]['level'];
            // Check that there is no level change between variable assignment and return
            for ($ptr = $variableAssignment + 1; $ptr <= $stackPtr; ++$ptr) {
                if ($level !== $tokens[$ptr]['level']) {
                    return;
                }
            }
            if ($phpcsFile->addFixableWarning(
                'Useless temporary variable to hold return value',
                $stackPtr,
                'UselessReturnVariable'
            )) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($variableAssignment, 'return');

                $ptr = $variableAssignment + 1;
                // Removing whitespaces
                while ($tokens[$ptr]['code'] === \T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($ptr++, '');
                }

                // Removing T_EQUAL
                $phpcsFile->fixer->replaceToken($ptr, '');

                // Removing old T_RETURN statement
                $phpcsFile->fixer->replaceToken($stackPtr, '');
                $ptr = $stackPtr + 1;

                while ($tokens[$ptr]['code'] !== \T_SEMICOLON) {
                    $phpcsFile->fixer->replaceToken($ptr++, '');
                }

                // Removing ';' of the old return statement
                $phpcsFile->fixer->replaceToken($ptr, '');

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
