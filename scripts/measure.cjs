const url = process.argv[2];
const width = Number(process.argv[3] || 390);
const height = Number(process.argv[4] || 900);
const port = 9333;

const { spawn } = require('node:child_process');
const path = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

const chrome = spawn(path, [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--user-data-dir=' + require('node:os').tmpdir() + '\\cdp-profile',
  `--remote-debugging-port=${port}`,
  `about:blank`,
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function getWsUrl() {
  for (let i = 0; i < 40; i++) {
    try {
      const res = await fetch(`http://127.0.0.1:${port}/json/version`);
      const json = await res.json();
      if (json.webSocketDebuggerUrl) return json.webSocketDebuggerUrl;
    } catch {}
    await sleep(250);
  }
  throw new Error('chrome did not start');
}

(async () => {
  const wsUrl = await getWsUrl();
  const ws = new WebSocket(wsUrl);
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
    const msg = JSON.parse(e.data);
    if (msg.id && pending.has(msg.id)) {
      const { resolve, reject } = pending.get(msg.id);
      pending.delete(msg.id);
      msg.error ? reject(new Error(JSON.stringify(msg.error))) : resolve(msg.result);
    }
  };

  const { targetId } = await send('Target.createTarget', { url: 'about:blank' });
  const { sessionId } = await send('Target.attachToTarget', { targetId, flatten: true });

  await send('Emulation.setDeviceMetricsOverride', {
    width,
    height,
    deviceScaleFactor: 1,
    mobile: true,
  }, sessionId);

  await send('Page.enable', {}, sessionId);
  await send('Page.navigate', { url }, sessionId);
  await sleep(2500);

  const expression = `(() => {
    const doc = document.documentElement;
    const vw = window.innerWidth;
    const offenders = [];
    document.querySelectorAll('*').forEach((el) => {
      const r = el.getBoundingClientRect();
      if (r.width === 0) return;
      if (r.right > vw + 1 || r.left < -1) {
        offenders.push({
          tag: el.tagName.toLowerCase(),
          cls: (el.className && el.className.baseVal !== undefined ? el.className.baseVal : el.className || '').toString().slice(0, 90),
          left: Math.round(r.left),
          right: Math.round(r.right),
          w: Math.round(r.width),
        });
      }
    });
    return JSON.stringify({
      innerWidth: vw,
      scrollWidth: doc.scrollWidth,
      bodyScrollWidth: document.body.scrollWidth,
      overflow: doc.scrollWidth > vw,
      offenders: offenders.slice(0, 14),
    }, null, 2);
  })()`;

  const { result } = await send('Runtime.evaluate', { expression, returnByValue: true }, sessionId);
  console.log(result.value);

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => {
  console.error('ERR', e.message);
  chrome.kill();
  process.exit(1);
});
