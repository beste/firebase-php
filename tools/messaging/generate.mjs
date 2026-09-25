import { createServer } from 'node:http';
import { loadEnvFile } from 'node:process';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const mode = process.argv[2] ?? 'fid';

if (mode === '--help') {
  console.log(`Usage: node tools/messaging/generate.mjs [fid|token]

Copy tools/messaging/.env.dist to tools/messaging/.env and fill it in,
or set its variables in your shell.
Run npm ci in tools/messaging first.
Chrome is required. The tool keeps a separate browser profile for each mode.`);
  process.exit(0);
}

if (mode !== 'fid' && mode !== 'token') {
  throw new Error('Choose fid or token');
}

try {
  loadEnvFile(new URL('./.env', import.meta.url));
} catch (error) {
  if (error.code !== 'ENOENT') {
    throw error;
  }
}

for (const name of [
  'FIREBASE_API_KEY',
  'FIREBASE_APP_ID',
  'FIREBASE_MESSAGING_SENDER_ID',
  'FIREBASE_PROJECT_ID',
  'FIREBASE_VAPID_KEY',
]) {
  if (!process.env[name]) {
    throw new Error(`${name} is required`);
  }
}

const config = {
  apiKey: process.env.FIREBASE_API_KEY,
  appId: process.env.FIREBASE_APP_ID,
  messagingSenderId: process.env.FIREBASE_MESSAGING_SENDER_ID,
  projectId: process.env.FIREBASE_PROJECT_ID,
};
const vapidKey = process.env.FIREBASE_VAPID_KEY;

// A stable origin lets the browser reuse its Firebase installation on later runs.
const origin = 'http://127.0.0.1:8765';
const server = createServer((request, response) => {
  if (request.url === '/') {
    response.writeHead(200, { 'Content-Type': 'text/html' });
    response.end('<!doctype html><title>FCM registration</title>');
  } else if (request.url === '/firebase-messaging-sw.js') {
    response.writeHead(200, { 'Content-Type': 'text/javascript' });
    response.end('');
  } else {
    response.writeHead(404);
    response.end();
  }
});

await new Promise((resolve, reject) => {
  server.once('error', reject);
  server.listen(8765, '127.0.0.1', resolve);
});

let browser;

try {
  const profile = fileURLToPath(new URL(`./.profiles/${mode}/`, import.meta.url));
  browser = await chromium.launchPersistentContext(profile, { channel: 'chrome', headless: false });
  await browser.grantPermissions(['notifications'], { origin });

  const page = await browser.newPage();
  await page.goto(origin);

  const id = await page.evaluate(async ({ config, mode, vapidKey }) => {
    const sdk = 'https://www.gstatic.com/firebasejs/12.19.0';
    const [{ initializeApp }, messagingSdk] = await Promise.all([
      import(`${sdk}/firebase-app.js`),
      import(`${sdk}/firebase-messaging.js`),
    ]);
    const messaging = messagingSdk.getMessaging(initializeApp(config));

    const registration = mode === 'token'
      ? messagingSdk.getToken(messaging, { vapidKey })
      : new Promise((resolve, reject) => {
        const unsubscribe = messagingSdk.onRegistered(messaging, fid => {
          unsubscribe();
          resolve(fid);
        });
        messagingSdk.register(messaging, { vapidKey }).catch(reject);
      });

    return Promise.race([
      registration,
      new Promise((_, reject) => setTimeout(() => reject(new Error('FCM registration timed out')), 60_000)),
    ]);
  }, { config, mode, vapidKey });

  if (!id) {
    throw new Error(`Firebase returned no ${mode}`);
  }

  console.log(id);
} finally {
  await browser?.close();
  await new Promise(resolve => server.close(resolve));
}
