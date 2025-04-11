<?php

declare(strict_types=1);

namespace WpOnepixStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

use function array_flip;
use function get_defined_functions;
use function in_array;
use function ltrim;
use function strrchr;
use function strtolower;
use function strtoupper;
use function substr;

use const T_AS;
use const T_NAMESPACE;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_USE;

/**
 * @internal
 */
trait NamespacesTrait
{
    /**
     * @return (
     *     $type is 'all' ? array{
     *         class: array<string, array{name: string, fqn: string, ptr: int}>,
     *         const?: array<string, array{name: string, fqn: string, ptr: int}>,
     *         function?: array<string, array{name: string, fqn: string, ptr: int}>
     *     } : array<string, array{name: string, fqn: string, ptr: int}>
     * )
     */
    private function getGlobalUses(File $phpcsFile, int $stackPtr = 0, string $type = 'class'): array
    {
        $first = 0;
        $last = $phpcsFile->numTokens;

        /** @var array<int, array{code: int, content?: string, comment_closer?: int, scope_closer?: int, scope_opener?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if (is_int($nsStart) && isset($tokens[$nsStart]['scope_opener']) && isset($tokens[$nsStart]['scope_closer'])) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last = $tokens[$nsStart]['scope_closer'];
        }

        $imports = [];

        $use = $first;
        while (($use = $phpcsFile->findNext(T_USE, $use + 1, $last)) !== false) {
            if (!UseStatement::isGlobalUse($phpcsFile, $use)) {
                continue;
            }

            $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, $use + 1, null, true);

            $useType = 'class';
            if (
                $tokens[$nextToken]['code'] === T_STRING
                && isset($tokens[$nextToken]['content'])
                && in_array(strtolower($tokens[$nextToken]['content']), ['const', 'function'], true)
            ) {
                $useType = strtolower($tokens[$nextToken]['content']);

                // increase token
                $nextToken = (int) $nextToken;
                $nextToken++;
            }

            if ($type !== 'all' && $type !== $useType) {
                continue;
            }

            $name = $this->getName($phpcsFile, (int) $nextToken);

            $endOfStatement = $phpcsFile->findEndOfStatement($use);
            $endOfName = $phpcsFile->findNext(
                Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING],
                (int) $nextToken + 1,
                null,
                true
            );

            $aliasStart = $phpcsFile->findNext(
                Tokens::$emptyTokens + [T_AS => T_AS],
                (int) $endOfName + 1,
                $endOfStatement,
                true
            );

            if ($aliasStart !== false && isset($tokens[$aliasStart]['content'])) {
                $alias = $tokens[$aliasStart]['content'];
            } else {
                $alias = $this->getAliasFromName($name);
            }

            $imports[$useType][$useType === 'const' ? strtoupper($alias) : strtolower($alias)] = [
                'name' => $alias,
                'fqn' => $name,
                'ptr' => $use,
            ];
        }

        return $type === 'all' ? $imports : ($imports[$type] ?? []);
    }

    private function getAliasFromName(string $name): string
    {
        $separatorPosition = strrchr($name, '\\');
        return $separatorPosition === false
            ? $name
            : substr($separatorPosition, 1);
    }

    private function getName(File $phpcsFile, int $stackPtr): string
    {
        /** @var array<int, array{code: int, content?: string, comment_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();

        $class = '';
        do {
            if (in_array($tokens[$stackPtr]['code'], Tokens::$emptyTokens, true)) {
                continue;
            }

            if (!in_array($tokens[$stackPtr]['code'], [T_NS_SEPARATOR, T_STRING], true)) {
                break;
            }

            if (!isset($tokens[$stackPtr]['content'])) {
                break;
            }
            $class .= $tokens[$stackPtr]['content'];
        } while (isset($tokens[++$stackPtr]));

        return ltrim($class, '\\');
    }

    private function getBuiltInFunctions(): array
    {
        $allFunctions = get_defined_functions();

        return array_flip($allFunctions['internal']);
    }
}
