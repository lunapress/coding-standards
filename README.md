<p align="left">
    <img src="https://img.shields.io/badge/PHP-%3E%3D8.3-8892BF?logo=php"  alt="PHP Version"/>
    <img src="https://github.com/lunapress/coding-standards/actions/workflows/unit-tests.yml/badge.svg" alt="PHPUnit Tests Status"/>
    <img src="https://coveralls.io/repos/github/lunapress/coding-standards/badge.svg?branch=main"  alt="Coverage"/>
    <img src="https://img.shields.io/badge/psalm-level%201-blue"  alt="Psalm level"/>
    <a href="https://packagist.org/packages/lunapress/coding-standards"><img src="https://img.shields.io/packagist/v/lunapress/coding-standards" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/lunapress/coding-standards"><img src="https://img.shields.io/packagist/dt/lunapress/coding-standards" alt="Total Downloads"></a>
</p>


# LunaPress Coding Standards

Based on [wp-coding-standards/wpcs](https://github.com/WordPress/WordPress-Coding-Standards) & [slevomat/coding-standard](https://github.com/slevomat/coding-standard)

- WP Sniffs
  - [LunaPressStandard.WP.ProceduralAbspath](#lunapressstandardwpproceduralabspath)

## Declarations Sniffs

## WP Sniffs

### LunaPressStandard.WP.ProceduralAbspath

Mandatory check `defined('ABSPATH') || exit;` is required in regular PHP files without PSR-4