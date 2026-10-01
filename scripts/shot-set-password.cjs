/**
 * Screenshot the loading mark on the set-password page.
 *
 * Creates an account as the admin, takes the one-time link out of the users
 * list, then opens it and shoots the moment the mark is on screen.
 *
 *   node scripts/shot-set-password.cjs
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

const base = 'http://127.0.0.1:8000';
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9354;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\setpw-${Date.now()}`,
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

  await send('Emulation.setDeviceMetricsOverride', { width: 900, height: 1000, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const evaluate = async (expression) => {
    const r = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }, S);
    return r?.result?.value;
  };

  const shoot = async (name) => {
    const s = await send('Page.captureScreenshot', { format: 'png' }, S);
    fs.writeFileSync(`${out}/${name}.png`, Buffer.from(s.data, 'base64'));
    console.log(`saved ${name}.png`);
  };

  // Sign in as the admin and read the one-time link off the users page.
  await send('Page.navigate', { url: `${base}/admin/login.php` }, S);
  await sleep(1800);
  await evaluate(`(async () => {
      const token = document.querySelector('meta[name="csrf-token"]').content;
      const form = document.querySelector('form');
      form.querySelector('[name=username]').value = 'admin';
      form.querySelector('[name=password]').value = 'password';
      form.append(new URLSearchParams({ _token: token }));
      form.submit();
  })()`);
  await sleep(2200);

  await send('Page.navigate', { url: `${base}/admin/users/create` }, S);
  await sleep(1800);
  console.log('at:', await evaluate('location.pathname'));
  await evaluate(`(async () => {
      const token = document.querySelector('meta[name="csrf-token"]').content;
      const form = document.querySelector('form');
      form.querySelector('[name=username]').value = 'setpw_demo';
      form.querySelector('[name=name]').value = 'Set Password Demo';
      form.querySelector('[name=email]').value = 'setpw_demo@goldengate.test';
      form.querySelector('[name=role]').value = 'staff';
      form.append(new URLSearchParams({ _token: token }));
      form.submit();
  })()`);
  await sleep(2200);
  console.log('after submit:', await evaluate('location.pathname'));
  console.log('errors:', await evaluate(`(() => {
      const e = [...document.querySelectorAll('.help-error')].map((n) => n.textContent.trim());
      return e.length ? e.join(' | ') : 'none';
  })()`));

  const link = await evaluate(`(() => {
      const input = document.querySelector('[data-copy]');
      return input ? input.dataset.copy : '';
  })()`);

  if (!link) {
    console.error('no set-password link found on the users page');
    ws.close();
    chrome.kill();
    process.exit(1);
  }

  console.log('link captured');

  // Fresh context: no admin session, so the page is the one a new person sees.
  await send('Page.navigate', { url: link }, S);
  await sleep(700);
  await shoot('set-password-loading');

  const state = await evaluate(`(() => {
      const loading = document.querySelector('[data-intro-loading]');
      const content = document.querySelector('[data-intro-content]');
      return 'loading hidden = ' + loading.hidden + ', content hidden = ' + content.hidden;
  })()`);
  console.log(state);

  await sleep(1600);
  await shoot('set-password-form');

  const after = await evaluate(`(() => {
      const loading = document.querySelector('[data-intro-loading]');
      const content = document.querySelector('[data-intro-content]');
      return 'loading hidden = ' + loading.hidden + ', content hidden = ' + content.hidden;
  })()`);
  console.log(after);

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });