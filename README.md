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
