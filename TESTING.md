# Testing

## Requirements

- PHP 8.3, 8.4, 8.5, or 8.6
- Composer
- Node.js 20.12 or later
- Google Chrome to generate registration tokens and FIDs
- A Java Development Kit (JDK) for emulator tests (CI uses Java 21)
- `jq` for the service account command in step 13, or another way to compact the JSON

From the project root, install the project dependencies and test tools:

```bash
composer setup
```

## Set up a Firebase project for live integration tests

1. Go to the [Firebase Console](https://console.firebase.google.com/) and create a new project. You don't need to enable
   Google AI assistance, or Google Analytics.
2. Go to the [project settings](https://console.firebase.google.com/u/0/project/_/settings/general)
   and note down the project id.
3. In the project settings, create a new web app. The app nickname doesn't matter. After creating the app, note down the
   `App ID`. From the app's SDK setup scripts, note down the `apiKey`.
4. Enable the [Firebase App Check API](https://console.cloud.google.com/apis/library/firebaseappcheck.googleapis.com)
   in the Google Cloud Console for the project.
5. Go to https://console.firebase.google.com/u/0/project/_/authentication/providers and enable the "Email/Password"
   (including "Email Link"), and "Anonymous" login methods.
6. Go to the [Cloud Messaging tab](https://console.firebase.google.com/u/0/project/_/settings/cloudmessaging/web) and
   note down the `Sender ID`.
7. On the same page, in the "Web configuration" and "Web push certificates" sections, generate a new key pair and note
   down the public key. This is the VAPID key.
8. Go to the [Service accounts tab](https://console.firebase.google.com/u/0/project/_/settings/serviceaccounts) and
   click on the "Generate new private key" button. This will download a JSON file containing your service account
   credentials.
9. Go to the [Realtime Database](https://console.firebase.google.com/u/0/project/_/database) and create a new database.
   Start it in "Lock mode" and note down the database URL. Note down the reference URL, it will look something like
   `https://<project-id>-default-rtdb.europe-west1.firebasedatabase.app/`
10. Go to the [Firestore Database](https://console.firebase.google.com/u/0/project/_/firestore) and create a new
    database. Select the "Standard edition" and choose a location. The location doesn't matter, but it makes sense to
    use the same as one you chose for the realtime database. Start it in "Production mode". Don't enable backups. This
    is the default Firestore database, it is named `(default)`. You don't need to note this one down.
11. (Optional) Create _another_ Firestore database. This will ask you to update your plan to a pay-as-you-go plan and to
    link the Firebase Project to a Cloud Billing account. I haven't been charged so far, but
    [usage on additional databases is billable](https://firebase.google.com/docs/firestore/pricing). If you don't create
    a second Firestore database, some tests will be skipped. Create a second Firestore and name it `custom` or another
    name, note it down.
12. (Optional) Create a new tenant in
    the [Google Cloud Console of your project](https://console.cloud.google.com/customer-identity/tenants). You will be
    asked to enable the Cloud Identity Platform API. Then go to
    the [Identity Platform settings](https://console.cloud.google.com/customer-identity/settings), go to the "Security"
    tab. In the "Multi tenancy" section, click on the "Allow tenants" button. You will then be redirected to the tenants
    overview. Add a tenant and note down the tenant ID. If you don't create a tenant, some tests will be skipped. If you
    do create a tenant, select it in Identity Platform and enable anonymous and email/password authentication under
    [Providers](https://console.cloud.google.com/customer-identity/providers).
13. Compact the service account JSON file you downloaded in step 8 to one line, for example with `jq`, and copy the
    output:

    ```bash
    jq -c . path/to/service-account.json
    ```

14. Copy `tools/messaging/.env.dist` to `tools/messaging/.env` and fill in the values you obtained in the previous
    steps:

    ```dotenv
    FIREBASE_PROJECT_ID="project id from step 2"
    FIREBASE_APP_ID="app id from step 3"
    FIREBASE_API_KEY="apiKey from step 3"
    FIREBASE_MESSAGING_SENDER_ID="Sender ID from step 6"
    FIREBASE_VAPID_KEY="VAPID key from step 7"
    ```

15. With Google Chrome installed, generate a registration token and a Firebase Installation ID (FID). Each command
    prints one value:

    ```bash
    node tools/messaging/generate.mjs token
    node tools/messaging/generate.mjs fid
    ```

16. Copy `tests/.env.dist` to `tests/.env` and fill in the values you obtained in the previous steps:

    ```dotenv
    TEST_FIREBASE_PROJECT_ID="project id from step 2"
    TEST_FIREBASE_APP_ID="app id from step 3"
    TEST_FIREBASE_RTDB_URI="database URL from step 9"
    TEST_FIREBASE_TENANT_ID="tenant ID from step 12, optional"
    TEST_FIREBASE_INSTALLATION_IDS='["<FID from step 15>"]'
    TEST_REGISTRATION_TOKENS='["<registration token from step 15>"]'
    TEST_FIRESTORE_CUSTOM_DB_NAME="custom Firestore database name from step 11, optional"
    GOOGLE_APPLICATION_CREDENTIALS='<one-line JSON from step 13>'
    ```

For GitHub Actions secrets, use the JSON values without the outer `.env` quotes.

PHPUnit loads `tests/.env` through `tests/bootstrap.php`. Use `composer reset-project` only with a test project: it
reads `tests/.env` and deletes Realtime Database data and Auth users.

## Unit tests and checks

Run `composer test:unit` for unit tests, `composer analyze` for static analysis, or `composer lint` for Rector,
PHP-CS-Fixer, and Composer normalization. `composer test:all` runs these three checks together.

## Integration tests

You should now be able to run the integration tests with `composer test:integration`.

## Emulator tests

After installing dependencies and tools with `composer setup`, run:

```bash
composer test:emulator
```

No Firebase project, service account, Firebase CLI login, or `tests/.env` is needed. The runner reads the exported
`TEST_FIREBASE_PROJECT_ID`, defaulting to `demo-firebase-php` when unset. The Database namespace is
`<project-id>-default-rtdb`. Use a `demo-` project ID to avoid Firebase CLI requests for a real project's configuration.
The emulator bootstrap ignores `tests/.env` and replaces inherited credentials with synthetic service account metadata
and a freshly generated, test-only RSA signing key for custom token tests. Separate tests explicitly remove credentials
to cover credentialless Auth and Database clients. Tenant tests use `demo-tenant`.

The command starts the Auth and Realtime Database emulators on ports `9099` and `9100`, runs the shared integration
test bodies in the `emulator` group, and stops the emulators afterward. It sets the emulator host variables for you.
The full live integration suite remains available through `composer test:integration` and the protected CI workflow.
Emulator tests run in ordinary pull request CI, including external contributor PRs, without secrets.

Additional PHPUnit options can be passed through the runner, for example:

```bash
composer test:emulator -- --filter=testSignInWithCustomToken
```

The Composer command disables Xdebug. To collect coverage, invoke the runner directly with `XDEBUG_MODE=coverage`,
as CI does. The runner accepts the same PHPUnit options:

```bash
XDEBUG_MODE=coverage php tests/bin/run-emulator-tests.php --coverage-clover=build/emulator-coverage.xml
```

Install dependencies and let the Firebase CLI download the Database emulator while online before testing offline.
Then disconnect external networking, leaving loopback available, and run `composer test:emulator` again. The tests must
pass without access to Firebase or Google APIs. Firebase CLI update checks or telemetry may still attempt external
connections; failures of those optional requests must not prevent the tests from passing.

## Coverage and pre-push checks

Run `composer test:coverage` to generate coverage for the full PHPUnit suite, including integration tests. Before
pushing, run `composer pre-push`, which fixes lint issues, runs static analysis, lint, unit tests, and a backward
compatibility check. Install and run the BC checker separately with PHP 8.5:

```bash
composer tools:bc:install
composer test:bc
```

Repositories without tags are skipped. The BC checker is excluded from `composer setup` and `composer tools:update`
because its locked dependencies do not support PHP 8.3 or 8.6. Update it with `composer tools:bc:update` under PHP 8.5.
CI installs the same lockfile.
