/**
 * Drive the checkout page: put something in the cart, then pick a township and
 * capture the delivery quote the customer actually sees.
 *
 *   node scripts/checkout-quote.cjs
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

const base = 'http://127.0.0.1:8000';
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9362;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\checkout-quote-${Date.now()}`,
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

  await send('Emulation.setDeviceMetricsOverride', { width: 1280, height: 1000, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const evaluate = async (expression) => {
    const { result, exceptionDetails } = await send('Runtime.evaluate', {
      expression, awaitPromise: true, returnByValue: true,
    }, S);

    if (exceptionDetails) {
      throw new Error(`${exceptionDetails.text} ${exceptionDetails.exception?.description || ''}`);
    }

    return result.value;
  };

  const go = async (url, wait = 2000) => {
    await send('Page.navigate', { url }, S);
    await sleep(wait);
  };

  const shot = async (name) => {
    const metrics = await send('Page.getLayoutMetrics', {}, S);
    const height = Math.min(metrics.cssContentSize.height, 2400);
    const s = await send('Page.captureScreenshot', {
      format: 'png',
      captureBeyondViewport: true,
      clip: { x: 0, y: 0, width: 1280, height, scale: 1 },
    }, S);

    fs.writeFileSync(`${out}/${name}.png`, Buffer.from(s.data, 'base64'));
    console.log(`saved ${name}.png (1280x${height})`);
  };

  // Add a sellable item, then go to checkout.
  await go(`${base}/catalog/fresh-produce`);
  const token = await evaluate(`document.querySelector('meta[name=csrf-token]').content`);

  const productId = await evaluate(`(() => {
    const ids = [...document.querySelectorAll('form[action$="/cart"]')]
      .map((f) => f.querySelector('[name=product_id]').value);
    return ids[0] ?? null;
  })()`);

  await evaluate(`fetch('/cart', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': ${JSON.stringify(token)} },
    body: new URLSearchParams({ product_id: ${JSON.stringify(productId)}, quantity: '2' }).toString(),
    credentials: 'same-origin',
  }).then((r) => r.status)`);

  await go(`${base}/checkout`);

  // The page marks itself ready only when every feature started. Without this
  // check a JS error upstream looks like a delivery quote that "just does not
  // update".
  const ready = await evaluate(`document.body.dataset.ready`);

  if (ready !== 'true') {
    throw new Error('page JavaScript did not finish booting (body[data-ready] is ' + ready + ')');
  }

  const read = `(() => {
    const text = (sel) => document.querySelector(sel)?.textContent.trim() ?? null;
    return {
      township: document.querySelector('[name=township]').value,
      delivery: text('[data-delivery-summary]'),
      total: text('[data-order-total]'),
      zone: text('[data-delivery-zone]'),
      eta: text('[data-delivery-eta]'),
      fee: text('[data-delivery-fee]'),
    };
  })()`;

  console.log('before picking: ', JSON.stringify(await evaluate(read)));
  await shot('25-checkout-unpicked');

  for (const town of ['Kamayut', 'Hlaing', 'Mandalay']) {
    await evaluate(`(() => {
      const el = document.querySelector('[name=township]');
      el.value = ${JSON.stringify(town)};
      el.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    })()`);
    await sleep(400);
    console.log(`${town}: `.padEnd(12), JSON.stringify(await evaluate(read)));
  }

  await shot('26-checkout-quoted');

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });
