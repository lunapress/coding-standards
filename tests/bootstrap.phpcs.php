<?php

require __DIR__ . '/../vendor/autoload.php';

/** @var array<string, string> $standardDirs */
$standardDirs = [];
/** @var array<string, string> $testDirs */
$testDirs = [];
/** @var array<string, bool> $sniffCodes */
$sniffCodes = [];
/** @var array<string, bool> $fixableCodes */
$fixableCodes = [];

$srcPath = __DIR__ . '/../WpOnepixStandard/';
$testPath = __DIR__ . '/../WpOnepixStandard/Tests/';

$allTestFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testPath));
$testFiles = new RegexIterator($allTestFiles, '/Test\.php$/');

/** @var SplFileInfo $file */
foreach ($testFiles as $file) {
    $content = file_get_contents($file->getPathname());
    if (!is_string($content)) {
        continue;
    }

    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
        $namespace = $matches[1];
    } else {
        $namespace = '';
    }

    if (preg_match('/class\s+(\w+)/', $content, $matches)) {
        $className = $matches[1];

        $fullClassName = $namespace ? $namespace . '\\' . $className : $className;

        $standardDirs[$fullClassName] = $srcPath;
        $testDirs[$fullClassName] = $testPath;
    }
}

$GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'] = $standardDirs;
$GLOBALS['PHP_CODESNIFFER_TEST_DIRS'] = $testDirs;
$GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'] = $sniffCodes;
$GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = $fixableCodes;

require __DIR__ . '/../vendor/squizlabs/php_codesniffer/tests/bootstrap.php';
