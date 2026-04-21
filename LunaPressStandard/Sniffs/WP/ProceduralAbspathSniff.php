<?php

declare(strict_types=1);

namespace LunaPressStandard\Sniffs\WP;

use Override;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final readonly class ProceduralAbspathSniff implements Sniff
{
    #[Override]
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    #[Override]
    public function process(
        File $phpcsFile,
        $stackPtr
    ): void {
        if ($phpcsFile->findNext(T_NAMESPACE, $stackPtr) !== false) {
            return;
        }

        $declarePtr = $phpcsFile->findNext(T_DECLARE, $stackPtr);

        if ($declarePtr !== false) {
            $insertPosition = $phpcsFile->findEndOfStatement($declarePtr);
        } else {
            $insertPosition = $stackPtr;
        }

        if ($this->hasDefinedAbspath($phpcsFile, $insertPosition + 1)) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            "Procedural files must contain defined('ABSPATH') || exit;",
            $stackPtr,
            'MissingProceduralAbspath'
        );

        if ($fix) {
            $this->applyFix($phpcsFile, $insertPosition, $declarePtr !== false);
        }
    }

    private function hasDefinedAbspath(File $phpcsFile, int $startPosition): bool
    {
        $definedPtr = $phpcsFile->findNext(T_STRING, $startPosition, null, false, 'defined');

        while ($definedPtr !== false) {
            $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $definedPtr + 1);

            if ($openParen !== false) {
                $stringPtr = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $openParen + 1, null, false, "'ABSPATH'");

                if ($stringPtr !== false) {
                    return true;
                }
            }

            $definedPtr = $phpcsFile->findNext(T_STRING, $definedPtr + 1, null, false, 'defined');
        }

        return false;
    }

    private function applyFix(File $phpcsFile, int $position, bool $afterDeclare): void
    {
        $phpcsFile->fixer->beginChangeset();

        $nextContent = $phpcsFile->findNext(T_WHITESPACE, $position + 1, null, true);

        $end = ($nextContent !== false) ? $nextContent : $phpcsFile->numTokens;

        for ($i = $position + 1; $i < $end; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        if ($afterDeclare) {
            $phpcsFile->fixer->addNewline($position);
        }

        $phpcsFile->fixer->addNewline($position);
        $phpcsFile->fixer->addContent($position, "defined('ABSPATH') || exit;");
        $phpcsFile->fixer->addNewline($position);
        $phpcsFile->fixer->addNewline($position);

        $phpcsFile->fixer->endChangeset();
    }
}
