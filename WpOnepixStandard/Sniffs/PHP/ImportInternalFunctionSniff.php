<?php

declare(strict_types=1);

namespace WpOnepixStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\Helper;
use WpOnepixStandard\Helper\NamespacesTrait;
use WpOnepixStandard\Helper\UseStatement;

use function in_array;
use function sort;
use function sprintf;

use const T_DOUBLE_COLON;
use const T_FUNCTION;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_WHITESPACE;

final class ImportInternalFunctionSniff implements Sniff
{
    use NamespacesTrait;

    /**
     * @var string[] Array of functions to exclude from importing.
     */
    public array $exclude = [];

    /**
     * @var array<string, array{name: string, fqn: string, ptr: int}> $importedFunctions
     */
    private array $importedFunctions = [];

    public function __construct()
    {
    }

    /**
     * @return int[]
     */
    #[\Override]
    public function register(): array
    {
        return [T_STRING];
    }

    private function setParameters(): void
    {
        $cliExclude = Helper::getConfigData('exclude') ?? [];
        $this->exclude = (array) $cliExclude;
    }

    /**
     * @param int $stackPtr
     * @return int
     */
    #[\Override]
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->setParameters();
        /** @var array<int, array{code: int, content?: string, comment_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();

        /** @var int|null $currentNamespacePtr */
        $currentNamespacePtr = null;
        $functionsToImport = [];

        do {
            $foundPtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr - 1);
            $namespacePtr = $foundPtr !== false ? $foundPtr : null;

            if ($namespacePtr !== $currentNamespacePtr) {
                if (is_int($currentNamespacePtr)) {
                    $this->importFunctions($phpcsFile, $currentNamespacePtr, $functionsToImport);
                }

                $currentNamespacePtr = $namespacePtr;
                $functionsToImport = [];

                $this->importedFunctions = $this->getGlobalUses(
                    $phpcsFile,
                    is_int($namespacePtr) ? $namespacePtr : 0,
                    'function'
                );

                foreach ($this->importedFunctions as $func) {
                    $fqn = $func['fqn'] ?? '';

                    if (in_array($fqn, $this->exclude, true)) {
                        $error = 'Function %s cannot be imported';
                        $data = [$func['fqn']];
                        $fix = $phpcsFile->addFixableError($error, $func['ptr'], 'ExcludeImported', $data);

                        if ($fix) {
                            $eos = $phpcsFile->findEndOfStatement($func['ptr']);

                            $phpcsFile->fixer->beginChangeset();
                            for ($i = $func['ptr']; $i <= $eos; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                            if ($tokens[$i + 1]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->replaceToken($i + 1, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }

            $functionName = $this->processString(
                $phpcsFile,
                $stackPtr,
                $namespacePtr !== null ? $namespacePtr : null
            );
            if ($functionName !== null) {
                $functionsToImport[] = $functionName;
            }
        } while ($stackPtr = $phpcsFile->findNext($this->register(), $stackPtr + 1));

        if (is_int($currentNamespacePtr)) {
            $this->importFunctions($phpcsFile, $currentNamespacePtr, $functionsToImport);
        }

        return $phpcsFile->numTokens + 1;
    }

    private function processString(File $phpcsFile, int $stackPtr, ?int $namespacePtr): ?string
    {
        /** @var array<int, array{code: int, content?: string, comment_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            return null;
        }

        $content = $tokens[$stackPtr]['content'] ?? '';

        $prev = $phpcsFile->findPrevious(
            Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR],
            $stackPtr - 1,
            null,
            true
        );
        if (
            $tokens[$prev]['code'] === T_FUNCTION
            || $tokens[$prev]['code'] === T_NEW
            || $tokens[$prev]['code'] === T_STRING
            || $tokens[$prev]['code'] === T_DOUBLE_COLON
            || $tokens[$prev]['code'] === T_OBJECT_OPERATOR
            || $tokens[$prev]['code'] === T_NULLSAFE_OBJECT_OPERATOR
        ) {
            return null;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_NS_SEPARATOR) {
            if ($namespacePtr === null) {
                $error = 'FQN for PHP internal function "%s" is not needed here, file does not have defined namespace';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NoNamespace', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (in_array($content, $this->exclude, true)) {
                $error = 'FQN for PHP internal function "%s" is not allowed here';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ExcludeRedundantFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->replaceToken($prev, '');
                }
            } elseif (isset($this->importedFunctions[$content]['fqn'])) {
                if ($this->importedFunctions[$content]['fqn'] === $content) {
                    $error = 'FQN for PHP internal function "%s" is not needed here, function is already imported';
                    $data = [
                        $content,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantFQN', $data);
                    if ($fix) {
                        $phpcsFile->fixer->replaceToken($prev, '');
                    }
                }
            } else {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ImportFQN', $data);
                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($prev, '');
                    $phpcsFile->fixer->endChangeset();

                    return $this->importFunction($content);
                }
            }
        } elseif ($namespacePtr !== null) {
            if (
                ! isset($this->importedFunctions[$content])
                && ! in_array($content, $this->exclude, true)
            ) {
                $error = 'PHP internal function "%s" must be imported';
                $data = [
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Import', $data);
                if ($fix) {
                    return $this->importFunction($content);
                }
            }
        }

        return null;
    }

    private function importFunction(string $functionName): string
    {
        $this->importedFunctions[$functionName] = [
            'name' => $functionName,
            'fqn' => $functionName,
            'ptr' => 0
        ];

        return $functionName;
    }

    /**
     * @param string[] $functionNames
     */
    private function importFunctions(File $phpcsFile, int $namespacePosition, array $functionNames): void
    {
        if (empty($functionNames)) {
            return;
        }

        /** @var array<int, array{code: int, content?: string, comment_closer?: int, scope_opener?: int, scope_closer?: int}> $tokens */
        $tokens = $phpcsFile->getTokens();
        $namespaceToken = $tokens[$namespacePosition];

        sort($functionNames);
        $phpcsFile->fixer->beginChangeset();

        $isBracketed = false;
        $scopeOpener = null;
        if (isset($namespaceToken['scope_opener'])) {
            $isBracketed = true;
            $scopeOpener = $namespaceToken['scope_opener'];
        }

        $namespaceEnd = $phpcsFile->findEndOfStatement($namespacePosition);
        $searchEnd = $isBracketed ? $scopeOpener : $namespaceEnd;
        $lastUseFunctionEnd = UseStatement::findLastUseAfterNamespace(
            $phpcsFile,
            $searchEnd
        );
        $lastUseEnd = UseStatement::findLastUseFunctionAfterNamespace(
            $phpcsFile,
            $searchEnd
        );
        if ($lastUseEnd === null) {
            $lastUseEnd = $lastUseFunctionEnd;
        }

        $insertAt = ($lastUseEnd ?? $searchEnd) + 1;

        $eol = $phpcsFile->eolChar;
        $importStatements = [];

        foreach ($functionNames as $functionName) {
            $importStatements[] = sprintf('use function %s;', $functionName);
        }

        $fullImportBlock = implode($eol, $importStatements);

        $prevLine = $phpcsFile->findPrevious([T_WHITESPACE], $insertAt - 1, null, true);
        if (
            $lastUseFunctionEnd === null
            && $prevLine !== false
            && $tokens[$insertAt]['line'] - $tokens[$prevLine]['line'] <= 1
        ) {
            $fullImportBlock = $eol . $fullImportBlock;
        }

        $nextLine = $phpcsFile->findNext([T_WHITESPACE], $insertAt, null, true);
        if ($nextLine !== false && $tokens[$nextLine]['line'] > $tokens[$insertAt]['line'] + 1) {
            $fullImportBlock .= $eol;
        } else {
            $fullImportBlock .= $eol . $eol;
        }

        $phpcsFile->fixer->addContent($insertAt, $fullImportBlock);
        $phpcsFile->fixer->endChangeset();
    }
}
