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
}
