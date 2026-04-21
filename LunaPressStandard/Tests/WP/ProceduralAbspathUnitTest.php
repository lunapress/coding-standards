<?php

declare(strict_types=1);

namespace LunaPressStandard\Tests\WP;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use LunaPressStandard\Sniffs\WP\ProceduralAbspathSniff;
use LunaPressStandard\Tests\CustomAbstractSniffUnitTestCase;

#[CoversClass(ProceduralAbspathSniff::class)]
final class ProceduralAbspathUnitTest extends CustomAbstractSniffUnitTestCase
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
            'ProceduralAbspathUnitTest.1.inc', 'ProceduralAbspathUnitTest.2.inc', => [
                1 => 1
            ],
            default => [],
        };
    }
}
