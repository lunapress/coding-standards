<?php

declare(strict_types=1);

namespace LunaPressStandard\Tests\WP;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use LunaPressStandard\Sniffs\WP\AbspathAfterNamespaceSniff;
use LunaPressStandard\Tests\CustomAbstractSniffUnitTestCase;

#[CoversClass(AbspathAfterNamespaceSniff::class)]
final class AbspathAfterNamespaceUnitTest extends CustomAbstractSniffUnitTestCase
{
    #[Override]
    public function getWarningList(): array
    {
        return [];
    }

    #[Override]
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
