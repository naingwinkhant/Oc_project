/**
 * Try to sign in as the pending demo account and report what the page says.
 *
 *   node scripts/check-pending-signin.cjs
 */

const { spawn } = require('node:child_process');
const os = require('node:os');

const base = 'http://127.0.0.1:8000';
const port = 9358;

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\pending-${Date.now()}`,
  `--remote-debugging-port=${port}`,
  'about:blank',
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

(async () => {
  let wsUrl;

  for (let i = 0; i < 50; i++) {
    try {
      const j = await (await fetch(`http://127.0.0.1:${port}/json/version`)).json();
      if (j.webSocketDebuggerUrl) { wsUrl = j.webSocketDebuggerUrl; break; }
    } catch { /* not up yet */ }
    await sleep(250);
  }

  const ws = new WebSocket(wsUrl);
  let id = 0;
  const pending = new Map();

  const send = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
    const msgId = ++id;
    pending.set(msgId, { resolve, reject });
    ws.send(JSON.stringify({ id: msgId, method, params, sessionId }));
  });

  await new Promise((r) => { ws.onopen = r; });
  ws.onmessage = (e) => {
    const m = JSON.parse(e.data);
    if (m.id && pending.has(m.id)) {
      const { resolve, reject } = pending.get(m.id);
      pending.delete(m.id);
      m.error ? reject(new Error(JSON.stringify(m.error))) : resolve(m.result);
    }
  };

  const { targetId } = await send('Target.createTarget', { url: 'about:blank' });
  const { sessionId: S } = await send('Target.attachToTarget', { targetId, flatten: true });

  await send('Emulation.setDeviceMetricsOverride', { width: 900, height: 900, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const evaluate = async (expression) => {
    const r = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }, S);
    return r?.result?.value;
  };

  const trySignIn = async (username, password) => {
    await send('Page.navigate', { url: `${base}/staff/login.php` }, S);
    await sleep(1800);
    await evaluate(`(() => {
        const form = document.querySelector('form');
        form.querySelector('[name=username]').value = ${JSON.stringify(username)};
        form.querySelector('[name=password]').value = ${JSON.stringify(password)};
        form.requestSubmit();
    })()`);
    await sleep(2000);

    return evaluate(`(() => {
        const error = document.querySelector('.help-error');
        return {
            at: location.pathname,
            error: error ? error.textContent.trim() : '(none)',
        };
    })()`);
  };

  const pendingTry = await trySignIn('pending_demo', 'chicken-fried-rice');
  console.log('pending account  :', pendingTry.at, '|', pendingTry.error);

  const wrongTry = await trySignIn('pending_demo', 'wrong-password');
  console.log('wrong password  :', wrongTry.at, '|', wrongTry.error);

  const goodTry = await trySignIn('ricky', 'password');
  console.log('accepted account:', goodTry.at, '|', goodTry.error);

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });