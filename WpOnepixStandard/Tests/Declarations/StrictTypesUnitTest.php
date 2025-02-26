<?php

declare(strict_types=1);

namespace WpOnepixStandard\Tests\Declarations;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use WpOnepixStandard\Sniffs\Declarations\StrictTypesSniff;

#[CoversClass(StrictTypesSniff::class)]
final class StrictTypesUnitTest extends AbstractSniffUnitTest
{
    #[\Override]
    public function getWarningList(): array
    {
        return [];
    }

    #[\Override]
    protected function getErrorList(string $testFile = ''): array
    {
        return match ($testFile) {
            'StrictTypesUnitTest.1.inc',
            'StrictTypesUnitTest.2.inc',
            'StrictTypesUnitTest.3.inc',
            'StrictTypesUnitTest.6.inc' => [
                1 => 1
            ],
            'StrictTypesUnitTest.4.inc',
            'StrictTypesUnitTest.5.inc' => [
                2 => 1
            ],
            default => [],
        };
    }
}
