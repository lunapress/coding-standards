# WP Static Analysis

### Required

- PHP 8.1+

## CLI

#### PHPCS

```shell
vendor/bin/wp-static-analysis phpcs
```

Uses the default configured rules from the package. To override, put your `ruleset.xml` rules file in the `config` folder of the project or use the `--config` option for a custom path.

`--config=./path/to/ruleset.dev.xml` - Path to the custom config file relative to the project

The rest of the arguments are the same as usual for [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer/wiki)