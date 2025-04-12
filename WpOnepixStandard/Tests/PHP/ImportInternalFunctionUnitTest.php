<?php

declare(strict_types=1);

namespace WpOnepixStandard\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use WpOnepixStandard\Sniffs\PHP\ImportInternalFunctionSniff;
use PHPCSUtils\BackCompat\Helper;

#[CoversClass(ImportInternalFunctionSniff::class)]
final class ImportInternalFunctionUnitTest extends AbstractSniffUnitTest
{
    #[\Override]
    protected function getErrorList(string $testFile = ''): array
    {
        switch ($testFile) {
            case 'ImportInternalFunctionUnitTest.1.inc':
                return [
                    4 => 1,
                    5 => 1,
                    11 => 1,
                    12 => 1,
                    18 => 1,
                    19 => 1,
                    26 => 1,
                    32 => 1,
                    41 => 1,
                    49 => 1,
                ];
            case 'ImportInternalFunctionUnitTest.2.inc':
                return [
                    5 => 1,
                    6 => 1,
                    8 => 1,
                    9 => 1,
                    11 => 1,
                ];
            case 'ImportInternalFunctionUnitTest.3.inc':
                return [
                    6 => 1,
                ];
            case 'ImportInternalFunctionUnitTest.4.inc':
                return [
                    5 => 1,
                    6 => 1,
                    9 => 1,
                    10 => 1,
                    12 => 1,
                ];
            case 'ImportInternalFunctionUnitTest.5.inc':
                return [
                    5 => 1,
                    7 => 1,
                    8 => 1,
                    16 => 1,
                    19 => 1,
                    23 => 1,
                ];
            case 'ImportInternalFunctionUnitTest.6.inc':
                return [
                    11 => 1,
                ];
        }

        return [
            5 => 1,
            7 => 1,
            11 => 1,
            21 => 1,
            24 => 1,
            25 => 1,
            27 => 1,
            29 => 1,
            30 => 1,
            32 => 1,
            33 => 1,
        ];
    }

    #[\Override]
    protected function getWarningList(string $testFile = ''): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setCliValues($filename, $config): void
    {
        if ($filename === 'ImportInternalFunctionUnitTest.5.inc') {
            Helper::setConfigData('exclude', ['array_reverse', 'key'], true, $config);
        }
    }
}
