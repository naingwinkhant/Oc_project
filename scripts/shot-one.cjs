/**
 * Screenshot a single URL at a given width.
 *
 *   node scripts/shot-one.cjs <url> <name> [width] [--click=<selector>]
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

const args = process.argv.slice(2);
const click = (args.find((a) => a.startsWith('--click=')) || '').replace('--click=', '');
const positional = args.filter((a) => !a.startsWith('--'));
const url = positional[0];
const name = positional[1] || 'shot';
const width = Number(positional[2] || 1280);
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9351;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\shot-one-${Date.now()}`,
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

  await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: width < 700 }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);
  await send('Page.navigate', { url }, S);
  await sleep(2200);

  if (click) {
    await send('Runtime.evaluate', {
      expression: `(() => { const el = document.querySelector(${JSON.stringify(click)}); if (!el) { throw new Error('no element for ${click}'); } el.click(); return true; })()`,
      awaitPromise: true,
    }, S);
    await sleep(700);
  }

  const metrics = await send('Page.getLayoutMetrics', {}, S);
  const height = Math.min(metrics.cssContentSize.height, 3000);
  const s = await send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    clip: { x: 0, y: 0, width, height, scale: 1 },
  }, S);

  fs.writeFileSync(`${out}/${name}.png`, Buffer.from(s.data, 'base64'));
  console.log(`saved ${name}.png (${width}x${height})`);

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });
