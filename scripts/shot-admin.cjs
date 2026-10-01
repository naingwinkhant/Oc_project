/**
 * Shoot an admin page after signing in as the admin.
 *
 *   node scripts/shot-admin.cjs <path> <name> [--theme=dark]
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

const args = process.argv.slice(2);
const theme = (args.find((a) => a.startsWith('--theme=')) || '').replace('--theme=', '');
const [path, name] = args.filter((a) => !a.startsWith('--'));

const base = 'http://127.0.0.1:8000';
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9355;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\admin-${Date.now()}`,
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

  if (theme) {
    await send('Page.navigate', { url: `${base}/admin/login.php` }, S);
    await sleep(1500);
    await evaluate(`localStorage.setItem('ggs.theme', '${theme}')`);
  }

  await send('Page.navigate', { url: `${base}/admin/login.php` }, S);
  await sleep(1800);
  await evaluate(`(() => {
      const form = document.querySelector('form');
      form.querySelector('[name=username]').value = 'admin';
      form.querySelector('[name=password]').value = 'password';
      form.requestSubmit();
  })()`);
  await sleep(2400);
  console.log('signed in at:', await evaluate('location.pathname'));

  await send('Page.navigate', { url: `${base}${path}` }, S);
  await sleep(2400);
  console.log('at:', await evaluate('location.pathname'));

  const metrics = await send('Page.getLayoutMetrics', {}, S);
  const height = Math.min(metrics.cssContentSize.height, 1800);
  const shot = await send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    clip: { x: 0, y: 0, width: 1440, height, scale: 1 },
  }, S);
  fs.writeFileSync(`${out}/${name}.png`, Buffer.from(shot.data, 'base64'));
  console.log(`saved ${name}.png`);

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });