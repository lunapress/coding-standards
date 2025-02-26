[![Latest Stable Version](http://poser.pugx.org/onepix/wp-coding-standards/v)](https://packagist.org/packages/onepix/wp-coding-standards) [![Total Downloads](http://poser.pugx.org/onepix/wp-coding-standards/downloads)](https://packagist.org/packages/onepix/wp-coding-standards) [![Latest Unstable Version](http://poser.pugx.org/onepix/wp-coding-standards/v/unstable)](https://packagist.org/packages/onepix/wp-coding-standards) [![License](http://poser.pugx.org/onepix/wp-coding-standards/license)](https://packagist.org/packages/onepix/wp-coding-standards) [![PHP Version Require](http://poser.pugx.org/onepix/wp-coding-standards/require/php)](https://packagist.org/packages/onepix/wp-coding-standards)

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
