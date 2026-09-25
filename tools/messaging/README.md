# Generate messaging test IDs

This tool registers a web app with Firebase Cloud Messaging and prints a Firebase Installation ID (FID) or a legacy
registration token. It uses Google Chrome without requiring browser clicks.

## Configure

Use Node.js 20.12 or later and Google Chrome. Install the tool dependency:

```sh
npm ci --prefix tools/messaging
cp -n tools/messaging/.env.dist tools/messaging/.env
```

In [Firebase Console](https://console.firebase.google.com/), open Project settings and create a web app if you do not
have one. Copy its `apiKey`, `appId`, `messagingSenderId`, and `projectId` into the corresponding variables in
`tools/messaging/.env`. The project ID must match the project used by your PHP integration test credentials.

Under Cloud Messaging > Web Push certificates, copy the public VAPID key to `FIREBASE_VAPID_KEY` in the same file.

The `.env` file is ignored by Git. Exported shell variables take precedence over its values, and the script also works without the file when all five variables are exported.

## Run

```sh
node tools/messaging/generate.mjs fid
node tools/messaging/generate.mjs token
```

Each command prints one ID. The tool keeps separate, ignored Chrome profiles in `tools/messaging/.profiles/` so later runs can reuse the same installation. Port `127.0.0.1:8765` must be free.

For the current PHP integration tests, put a generated registration token in the ignored `tests/.env` as `TEST_REGISTRATION_TOKENS='["the-token"]'`. Give CI the same JSON array through its `TEST_REGISTRATION_TOKENS` secret. ID generation is not part of the CI workflow.
