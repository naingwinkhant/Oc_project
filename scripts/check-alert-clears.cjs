/**
 * Press Complete on an order and check the bell entry goes with it.
 *
 *   node scripts/check-alert-clears.cjs
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

const base = 'http://127.0.0.1:8000';
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9356;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\clears-${Date.now()}`,
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

  await send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 1000, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const evaluate = async (expression) => {
    const r = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }, S);
    return r?.result?.value;
  };

  const shoot = async (name) => {
    const s = await send('Page.captureScreenshot', { format: 'png' }, S);
    fs.writeFileSync(`${out}/${name}.png`, Buffer.from(s.data, 'base64'));
  };

  const openBell = async () => {
    await evaluate(`document.querySelector('[data-toggle="notifications-panel"]').click()`);
    await sleep(500);
  };

  const bellText = () => evaluate(`(() => {
      const panel = document.getElementById('notifications-panel');
      const rows = [...panel.querySelectorAll('[data-notification-item]')].map((n) => n.textContent.replace(/\\s+/g, ' ').trim().slice(0, 60));
      const badge = document.querySelector('[data-notification-badge]');
      return 'badge=' + (badge.hidden ? '(none)' : badge.textContent) + ' | ' + rows.join(' // ');
  })()`);

  // Sign in as the admin.
  await send('Page.navigate', { url: `${base}/admin/login.php` }, S);
  await sleep(1800);
  await evaluate(`(() => {
      const form = document.querySelector('form');
      form.querySelector('[name=username]').value = 'admin';
      form.querySelector('[name=password]').value = 'password';
      form.requestSubmit();
  })()`);
  await sleep(2400);

  // Find a paid order that still has its Complete button.
  await send('Page.navigate', { url: `${base}/admin/orders` }, S);
  await sleep(2200);

  const target = await evaluate(`(() => {
      const rows = [...document.querySelectorAll('tbody tr')];
      for (const row of rows) {
          const button = [...row.querySelectorAll('button')].find((b) => b.textContent.includes('Complete'));
          if (button) {
              const number = row.querySelector('a').textContent.trim();
              button.closest('form').submit = () => {};
              return number;
          }
      }
      return '';
  })()`);

  console.log('target order:', target || '(none with a Complete button)');

  if (!target) {
    ws.close(); chrome.kill(); process.exit(1);
  }

  // What the bell says before.
  await send('Page.navigate', { url: `${base}/admin` }, S);
  await sleep(2200);
  await openBell();
  console.log('bell BEFORE:', await bellText());
  await shoot('alert-before');

  // Press Complete on that order.
  await send('Page.navigate', { url: `${base}/admin/orders` }, S);
  await sleep(2200);
  const pressed = await evaluate(`(() => {
      const rows = [...document.querySelectorAll('tbody tr')];
      for (const row of rows) {
          const number = row.querySelector('a').textContent.trim();
          if (number !== ${JSON.stringify(target)}) continue;
          const button = [...row.querySelectorAll('button')].find((b) => b.textContent.includes('Complete'));
          if (!button) return 'no Complete button on ' + number;
          // Skip the confirm() the button asks for.
          window.confirm = () => true;
          button.closest('form').requestSubmit();
          return 'pressed on ' + number;
      }
      return 'row not found';
  })()`);
  console.log('action:', pressed);
  await sleep(2400);
  console.log('at after:', await evaluate('location.pathname'));

  await send('Page.navigate', { url: `${base}/admin` }, S);
  await sleep(2200);
  await openBell();
  const after = await bellText();
  console.log('bell AFTER: ', after);
  await shoot('alert-after');

  console.log(after.includes(target) ? 'STILL SHOWING THE ORDER' : 'the order alert is gone');

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });