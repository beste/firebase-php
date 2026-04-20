# Contributing

## Before you start

Please do not introduce changes without talking to the repository maintainers first. That way nobody spends time on a pull request that cannot be merged.

If you want to provide a bug fix, include tests that prove the bug exists and that it is fixed.

## Local setup

1. Install project dependencies:

   ```bash
   composer setup
   ```

2. Create local test env config from [tests/.env.dist](../tests/.env.dist):

   ```bash
   cp tests/.env.dist tests/.env
   ```

3. Fill in the required values in `tests/.env`:

   - `GOOGLE_APPLICATION_CREDENTIALS`
   - `TEST_FIREBASE_APP_ID`
   - `TEST_FIREBASE_RTDB_URI`
   - `TEST_FIREBASE_TENANT_ID`
   - `TEST_REGISTRATION_TOKENS`
   - `TEST_FIRESTORE_CUSTOM_DB_NAME` if you use a custom Firestore database

4. If you run emulator tests, install the Firebase CLI and make sure it can start Auth and Realtime Database emulators. The test suite uses ports `9099` and `9100`.

   ```bash
   composer test:emulator
   ```

## Useful commands

### Lint

```bash
composer lint
```

Runs Rector, PHP-CS-Fixer, and composer normalization checks.

### Typecheck / static analysis

```bash
composer analyze
```

Runs PHPStan.

### Tests

```bash
composer test:unit
composer test:integration
composer test:emulator
composer test:all
```

Before pushing, run:

```bash
composer pre-push
```

This runs lint fixes, full tests, and backward-compat checks.

### Coverage

```bash
composer test:coverage
```

## Notes

- `tests/bootstrap.php` loads `tests/.env` automatically.
- `tests/bin/reset-project` also reads `tests/.env` and requires `GOOGLE_APPLICATION_CREDENTIALS`.
  Use only with test or integration projects. It deletes Realtime Database data and Auth users.
  Do not run against production projects.
- Keep changes focused and follow existing code style.

## Thanks

Support the project’s development and keep it sustainable by becoming a [GitHub Sponsor](https://github.com/sponsors/jeromegamez).
