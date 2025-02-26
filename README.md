<p align="center">
    <img src="https://img.shields.io/badge/PHP-%3E%3D8.1-8892BF?logo=php"  alt="PHP Version"/>
    <img src="https://img.shields.io/badge/psalm-level%201-blue"  alt="Psalm level"/>
    <a href="https://packagist.org/packages/onepix/wp-coding-standards"><img src="https://img.shields.io/packagist/v/onepix/wp-coding-standards" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/onepix/wp-coding-standards"><img src="https://img.shields.io/packagist/dt/onepix/wp-coding-standards" alt="Total Downloads"></a>
    <img src="https://img.shields.io/github/actions/workflow/status/onepixnet/wp-coding-standards/unit-tests.yml"  alt="Unit tests"/>
</p>

![Coverage](https://coveralls.io/repos/github/onepixnet/wp-coding-standards/badge.svg?branch=main)

# WP Coding Standards

Based on [wp-coding-standards/wpcs](https://github.com/WordPress/WordPress-Coding-Standards).

- Declarations Sniffs
  - [WpOnepixStandard.Declarations.StrictTypes](#wponepixstandarddeclarationsstricttypes)
- WP Sniffs
  - [WpOnepixStandard.WP.AbspathAfterNamespace](#wponepixstandardwpabspathafternamespace)

## Declarations Sniffs

### WpOnepixStandard.Declarations.StrictTypes

Checks for mandatory `declare(strict_types=1);` after `<?php`. Takes into account other parameters besides `strict_types`

## WP Sniffs

### WpOnepixStandard.WP.AbspathAfterNamespace

Looking for a mandatory `defined('ABSPATH') || exit;` check after `namespace` so that you can't go to the file directly from the url
