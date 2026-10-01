/**
 * Check the Appearance picker keeps up with the header shortcut.
 *
 *   node scripts/check-theme-picker.cjs
 */

const { spawn } = require('node:child_process');
const os = require('node:os');

const base = 'http://127.0.0.1:8000';
const port = 9357;

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\picker-${Date.now()}`,
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

  await send('Emulation.setDeviceMetricsOverride', { width: 1100, height: 1000, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const evaluate = async (expression) => {
    const r = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true }, S);
    return r?.result?.value;
  };

  const state = () => evaluate(`(() => {
      const chosen = [...document.querySelectorAll('[data-theme-choice]')]
          .filter((i) => i.checked)
          .map((i) => i.value);
      const ringed = [...document.querySelectorAll('[data-theme-options] label')]
          .filter((l) => l.classList.contains('ring-brand-600'))
          .map((l) => l.querySelector('[data-theme-choice]').value);
      return 'dark=' + document.documentElement.classList.contains('dark')
          + ' checked=' + (chosen.join(',') || 'none')
          + ' highlighted=' + (ringed.join(',') || 'none');
  })()`);

  await send('Page.navigate', { url: `${base}/settings` }, S);
  await sleep(2600);
  console.log('on load (system is the default):');
  console.log('  ', await state());

  // Use the header shortcut, which is what left System marked as chosen.
  await evaluate(`document.querySelector('[data-theme-toggle]').click()`);
  await sleep(700);
  console.log('after pressing the header shortcut once:');
  console.log('  ', await state());

  await evaluate(`document.querySelector('[data-theme-toggle]').click()`);
  await sleep(700);
  console.log('after pressing it again:');
  console.log('  ', await state());

  // Reload: the panel must still agree with what is being painted.
  await send('Page.navigate', { url: `${base}/settings` }, S);
  await sleep(2600);
  console.log('after a reload:');
  console.log('  ', await state());

  // Pick a radio directly.
  await evaluate(`document.querySelector('[data-theme-choice][value="dark"]').click()`);
  await sleep(800);
  console.log('after choosing Night in the panel:');
  console.log('  ', await state());

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });