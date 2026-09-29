const url = process.argv[2];
const out = process.argv[3];
const width = Number(process.argv[4] || 390);
const height = Number(process.argv[5] || 844);
const email = process.argv[6] || null;
const port = 9334;

const { spawn } = require('node:child_process');
const os = require('node:os');
const fs = require('node:fs');

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  '--user-data-dir=' + os.tmpdir() + '\\cdp-shot-' + Date.now(),
  `--remote-debugging-port=${port}`,
  'about:blank',
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function wsUrl() {
  for (let i = 0; i < 50; i++) {
    try {
      const r = await fetch(`http://127.0.0.1:${port}/json/version`);
      const j = await r.json();
      if (j.webSocketDebuggerUrl) return j.webSocketDebuggerUrl;
    } catch {}
    await sleep(250);
  }
  throw new Error('chrome did not start');
}

(async () => {
  const ws = new WebSocket(await wsUrl());
  let id = 0;
  const pending = new Map();
  const send = (method, params = {}, sessionId) =>
    new Promise((resolve, reject) => {
      const msgId = ++id;
      pending.set(msgId, { resolve, reject });
      ws.send(JSON.stringify({ id: msgId, method, params, sessionId }));
    });

  await new Promise((r) => (ws.onopen = r));
  ws.onmessage = (e) => {
    const m = JSON.parse(e.data);
    if (m.id && pending.has(m.id)) {
      const { resolve, reject } = pending.get(m.id);
      pending.delete(m.id);
      m.error ? reject(new Error(JSON.stringify(m.error))) : resolve(m.result);
    }
  };

  const { targetId } = await send('Target.createTarget', { url: 'about:blank' });
  const { sessionId } = await send('Target.attachToTarget', { targetId, flatten: true });

  await send('Emulation.setDeviceMetricsOverride', { width, height, deviceScaleFactor: 1, mobile: width < 768 }, sessionId);
  await send('Page.enable', {}, sessionId);
  await send('Runtime.enable', {}, sessionId);

  if (email) {
    await send('Page.navigate', { url: 'http://127.0.0.1:8000/login' }, sessionId);
    await sleep(1500);
    await send('Runtime.evaluate', {
      awaitPromise: true,
      expression: `(async () => {
        const form = document.querySelector('form[action$="/login"]');
        const fd = new FormData(form);
        // The form field is "username"; the backend also accepts an email.
        fd.set('username', ${JSON.stringify(email)});
        fd.set('password', 'password');
        fd.set('_token', form.querySelector('input[name="_token"]').value);
        const res = await fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin', redirect: 'follow' });
        await res.text();
        return document.cookie.length;
      })()`,
    }, sessionId);

    let signedIn = false;
    for (let i = 0; i < 20 && !signedIn; i++) {
      await send('Page.navigate', { url: 'http://127.0.0.1:8000/admin' }, sessionId);
      await sleep(700);
      const check = await send('Runtime.evaluate', {
        expression: `document.querySelector('aside#sidebar') ? 'in' : 'out'`,
        returnByValue: true,
      }, sessionId);
      signedIn = check.result.value === 'in';
    }

    if (!signedIn) {
      console.error('LOGIN FAILED for ' + email);
    }
  }

  await send('Page.navigate', { url }, sessionId);
  await sleep(2600);

  const metrics = await send('Page.getLayoutMetrics', {}, sessionId);
  const full = Math.min(metrics.cssContentSize.height, 4000);

  const shot = await send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    clip: { x: 0, y: 0, width, height: full, scale: 1 },
  }, sessionId);

  fs.writeFileSync(out, Buffer.from(shot.data, 'base64'));
  console.log('saved ' + out + ' (' + width + 'x' + full + ')');

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => {
  console.error('ERR', e.message);
  chrome.kill();
  process.exit(1);
});
