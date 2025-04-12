<?php

declare(strict_types=1);

namespace WpOnepixStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

final class UseStatement
{
    public static function isGlobalUse(File $phpcsFile, int $stackPtr): bool
    {
        /** @var array<int, array{code: int}> $tokens */
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS])) {
            return false;
        }

        return true;
    }

    public static function findLastUseAfterNamespace(File $phpcsFile, int $namespaceEnd): ?int
    {
        /** @var array<int, array{code: int, content?: string, comment_closer?: int, scope_opener?: int, scope_closer?: int}> $tokens */
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

    public static function findLastUseFunctionAfterNamespace(File $phpcsFile, int $namespaceEnd): ?int
    {
        /** @var array<int, array{code: int, content?: string, comment_closer?: int, scope_opener?: int, scope_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();
        $currentPosition = $namespaceEnd + 1;
        $lastUseFunctionEnd = null;

        while ($nextUse = $phpcsFile->findNext(T_USE, $currentPosition, null, false, null, true)) {
            if (!empty($tokens[$nextUse]['conditions'])) {
                break;
            }

            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $nextUse + 1, null, true);
            $end = $phpcsFile->findEndOfStatement($nextUse);

            if ($tokens[$nextToken]['code'] === T_FUNCTION) {
                $lastUseFunctionEnd = $end;
            }

            $currentPosition = $end + 1;
        }

        return $lastUseFunctionEnd;
    }
}
