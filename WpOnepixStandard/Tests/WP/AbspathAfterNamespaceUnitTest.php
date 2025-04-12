<?php

declare(strict_types=1);

namespace WpOnepixStandard\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use WpOnepixStandard\Sniffs\WP\AbspathAfterNamespaceSniff;

#[CoversClass(AbspathAfterNamespaceSniff::class)]
final class AbspathAfterNamespaceUnitTest extends AbstractSniffUnitTest
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
            'AbspathAfterNamespaceUnitTest.1.inc', => [
                3 => 1
            ],
            'AbspathAfterNamespaceUnitTest.3.inc', => [
                4 => 1
            ],
            default => [],
        };
    }
}
