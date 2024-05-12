# Sample README

## Setup

Setup uses the [GitLab script
system](https://github.blog/2015-06-30-scripts-to-rule-them-all/). To get
started, run:

```
script/setup
```

## Linting, fixing, and testing

### PHP

The PHP lint scripts are run with:

```
composer lint
```

To apply automatic fixes, run:

```
composer lint:fix
```

To execute unit tests, run:

```
composer test
```

### JavaScript

The JavaScript lint scripts are run with:

```
npm run lint
```

To apply automatic fixes, run:

```
npm run lint:fix
```

To execute unit tests, run:

```
npm test
```

### Pre-commit

The lint and tests are also run automatically by `.husky/pre-commit`. You must
fix any errors or re-run the commit with `--no-verify` to bypass the check.

### Commitlint

Commitlint is run automatically by `.husky/pre-commit` to apply [conventional
commits](https://www.npmjs.com/package/@commitlint/config-conventional). Fix
the lint errors before committing.
