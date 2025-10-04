<?php

declare(strict_types=1);

namespace WpOnepixStandard\Sniffs\WP;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class AbspathAfterNamespaceSniff implements Sniff
{
    private const REQUIRED_CONSTANT = "'ABSPATH'";
    private const EXPECTED_SEQUENCE = [
        // defined
        T_STRING,
        // (
        T_OPEN_PARENTHESIS,
        // 'ABSPATH'
        T_CONSTANT_ENCAPSED_STRING,
        // )
        T_CLOSE_PARENTHESIS,
        // ||
        T_BOOLEAN_OR,
        // exit
        T_EXIT,
        // ;
        T_SEMICOLON
    ];

    #[Override]
    public function register(): array
    {
        return [T_NAMESPACE];
    }

    #[Override]
    public function process(
        File $phpcsFile,
        $stackPtr
    ): void {
        $namespaceEnd = $phpcsFile->findEndOfStatement($stackPtr);
        $lastUseEnd = $this->findLastUseAfterNamespace($phpcsFile, $namespaceEnd);
        $startPosition = ($lastUseEnd ?? $namespaceEnd) + 1;

        $found = $this->findDefinedABSPATHSequence($phpcsFile, $startPosition);

        if (!$found) {
            $fix = $phpcsFile->addFixableError(
                'After the namespace there should be a line defined(\'ABSPATH\') || exit;',
                $namespaceEnd,
                'MissingABSPATHCheck'
            );

            if ($fix) {
                $this->applyFix($phpcsFile, $startPosition - 1);
            }
        }
    }

    private function findDefinedABSPATHSequence(
        File $phpcsFile,
        int $position
    ): bool {
        /** @var array<int, array{code: int, content?: string, comment_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();
        $currentPosition = $position;

        foreach (self::EXPECTED_SEQUENCE as $expectedTokenCode) {
            // Skip spaces and comments
            $currentPosition = $phpcsFile->findNext(
                [T_WHITESPACE, T_COMMENT],
                $currentPosition,
                null,
                true
            );

            if ($currentPosition === false) {
                return false;
            }

            $currentToken = $tokens[$currentPosition];

            // Checking compliance with the token code
            if ($currentToken['code'] !== $expectedTokenCode) {
                return false;
            }

            // Additional content checks for specific tokens
            if (isset($currentToken['content'])) {
                switch ($expectedTokenCode) {
                    case T_STRING:
                        if (strtolower($currentToken['content']) !== 'defined') {
                            return false;
                        }
                        break;
                    case T_CONSTANT_ENCAPSED_STRING:
                        if ($currentToken['content'] !== self::REQUIRED_CONSTANT) {
                            return false;
                        }
                        break;
                }
            }

            $currentPosition++;
        }

        return true;
    }

    private function findLastUseAfterNamespace(File $phpcsFile, int $namespaceEnd): ?int
    {
        $tokens = $phpcsFile->getTokens();
        $currentPosition = $namespaceEnd + 1;
        $lastUseEnd = null;

        while ($nextUse = $phpcsFile->findNext(T_USE, $currentPosition, null, false, null, true)) {
            if (!empty($tokens[$nextUse]['conditions'])) {
                break;
            }
            $lastUseEnd = $phpcsFile->findEndOfStatement($nextUse);
            $currentPosition = $lastUseEnd + 1;
        }

        return $lastUseEnd;
    }

    private function applyFix(File $phpcsFile, int $namespaceEnd): void
    {
        $phpcsFile->fixer->beginChangeset();

        $phpcsFile->fixer->addNewline($namespaceEnd);
        $phpcsFile->fixer->addNewline($namespaceEnd);
        $phpcsFile->fixer->addContent(
            $namespaceEnd,
            "defined('ABSPATH') || exit;"
        );

        $phpcsFile->fixer->endChangeset();
    }
}
