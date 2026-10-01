/**
 * Drives a browser through add-to-cart -> cart -> checkout -> sandbox payment,
 * capturing screenshots at each step so the flow can be eyeballed.
 *
 *   node scripts/shop-flow.cjs [outputDir]
 */

const fs = require('node:fs');
const os = require('node:os');
const { spawn } = require('node:child_process');

// Where the dev server is listening. Overridable so this can be pointed at
// whatever port the server was started on.
const base = process.env.SHOP_BASE || 'http://127.0.0.1:8000';
const out = process.argv[2] || 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9345;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\shop-flow-${Date.now()}`,
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
    } catch {
      // chrome not up yet
    }
    await sleep(250);
  }
  throw new Error('chrome did not start');
}

(async () => {
  const ws = new WebSocket(await wsUrl());
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

  await send('Emulation.setDeviceMetricsOverride', { width: 1280, height: 900, deviceScaleFactor: 1, mobile: false }, S);
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

  const go = async (url) => {
    await send('Page.navigate', { url }, S);
    await sleep(1900);
  };

  const shot = async (name) => {
    const metrics = await send('Page.getLayoutMetrics', {}, S);
    const height = Math.min(metrics.cssContentSize.height, 2600);
    const s = await send('Page.captureScreenshot', {
      format: 'png',
      captureBeyondViewport: true,
      clip: { x: 0, y: 0, width: 1280, height, scale: 1 },
    }, S);

    fs.writeFileSync(`${out}/${name}.png`, Buffer.from(s.data, 'base64'));
    console.log(`saved ${name}.png (1280x${height})`);
  };

  await go(`${base}/catalog/fresh-produce`);
  await shot('1-catalogue');

  const token = await evaluate(`document.querySelector('meta[name=csrf-token]').content`);

  // Submit one request at a time. Calling form.submit() in a loop races the
  // browser: each call starts a navigation that cancels the previous one, so
  // only the last form survives.
  const post = (path, body) => evaluate(`fetch(${JSON.stringify(path)}, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': ${JSON.stringify(token)},
      },
      body: new URLSearchParams(${JSON.stringify({ ...body, _token: token })}).toString(),
      credentials: 'same-origin',
      redirect: 'follow',
    }).then((r) => r.url)`);

  const idsFor = (action) => evaluate(
    `[...document.querySelectorAll('form[action$=${JSON.stringify(action)}]')]
       .map((f) => f.querySelector('[name=product_id]').value)`,
  );

  for (const id of (await idsFor('/favourites/toggle')).slice(0, 2)) {
    console.log('favourite', id, '->', await post('/favourites/toggle', { product_id: id, return_to: '/' }));
  }

  const cartForms = await idsFor('/cart');

  if (cartForms.length === 0) {
    throw new Error('no add-to-cart forms found on the catalogue page');
  }

  for (const [index, id] of cartForms.slice(0, 3).entries()) {
    console.log('add', id, '->', await post('/cart', { product_id: id, quantity: index === 1 ? 2 : 1 }));
  }

  await go(`${base}/cart`);
  await shot('2-cart');

  const cartState = await evaluate(`(() => {
    const body = document.body.innerText;
    return {
      empty: /Your cart is empty/.test(body),
      summary: (body.match(/(\\d+) items? ready to check out/) || [])[1] || null,
      checkout: !!document.querySelector('a[href$="/checkout"]'),
    };
  })()`);

  if (cartState.empty || !cartState.checkout) {
    throw new Error(`cart did not take the items: ${JSON.stringify(cartState)}`);
  }

  console.log('cart holds', cartState.summary, 'items');

  await go(`${base}/checkout`);

  const checkoutState = await evaluate(`(() => ({
    url: location.href,
    form: !!document.querySelector('form[action$="/checkout"]'),
    redirect: /Your cart is empty|no longer be sold/.test(document.body.innerText),
  }))()`);

  if (!checkoutState.form) {
    throw new Error(`checkout form missing: ${JSON.stringify(checkoutState)}`);
  }

  const checkoutToken = await evaluate(`document.querySelector('meta[name=csrf-token]').content`);

  const orderUrl = await evaluate(`fetch('/checkout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': ${JSON.stringify(checkoutToken)},
      },
      body: new URLSearchParams(${JSON.stringify({
        _token: checkoutToken,
        customer_name: 'Aung Kyaw',
        phone: '09 380 000 00',
        email: 'aung@example.com',
        delivery_address: 'No. 12, Baho Road, Kamayut',
        township: 'Mandalay',
        note: 'Call before arriving',
        payment_gateway: 'sandbox',
      })}).toString(),
      credentials: 'same-origin',
      redirect: 'follow',
    }).then((r) => r.url)`);

  console.log('order ->', orderUrl);

  await go(orderUrl);
  await sleep(800);
  await shot('3-order-pending');

  const sandboxUrl = await evaluate(`(() => {
    const a = [...document.querySelectorAll('a')].find((x) => x.href.includes('/payments/sandbox/'));
    return a ? a.href : null;
  })()`);

  if (sandboxUrl) {
    await go(sandboxUrl);
    await shot('4-sandbox-pay');

    // Must click, not form.submit(): the latter bypasses the submit button, so
    // the outcome=paid field never reaches the server.
    await evaluate(`(() => { document.querySelector('button[value=paid]').click(); return true; })()`);
    await sleep(2600);

    const settled = await evaluate(`!!document.querySelector('a[href$="/catalog"]') && /Payment received/.test(document.body.innerText)`);

    if (!settled) {
      throw new Error(`sandbox payment did not settle: ${await evaluate('location.href')}`);
    }

    await shot('5-order-paid');
  } else {
    console.log('sandbox link not found');
  }

  await go(`${base}/catalog/new-arrivals`);
  await shot('6-new-arrivals');

  await go(`${base}/favourites`);
  await shot('7-favourites');

  console.log('flow complete');
  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => {
  console.error('ERR', e.message);
  chrome.kill();
  process.exit(1);
});
