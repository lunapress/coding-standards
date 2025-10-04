<?php

declare(strict_types=1);

namespace WpOnepixStandard\Tests;

use Override;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

abstract class CustomAbstractSniffUnitTestCase extends AbstractSniffUnitTest
{
    #[Override]
    protected function setUp(): void
    {
        $this->setUpPrerequisites();
    }
}
