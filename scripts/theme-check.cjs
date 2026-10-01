/**
 * Report the colours the browser actually paints in day and night mode, and
 * save a screenshot of each so the pair can be compared by eye.
 *
 *   node scripts/theme-check.cjs <url> [name]
 */

const fs = require('node:fs');
const { spawn } = require('node:child_process');
const os = require('node:os');

const url = process.argv[2] || 'http://127.0.0.1:8000/';
const name = process.argv[3] || 'theme';
const out = 'C:/Users/User/AppData/Local/Temp/opencode/shots';
const port = 9352;

fs.mkdirSync(out, { recursive: true });

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${os.tmpdir()}\\theme-check-${Date.now()}`,
  `--remote-debugging-port=${port}`,
  'about:blank',
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const PROBE = `(() => {
    try {
        const root = getComputedStyle(document.documentElement);
        const body = getComputedStyle(document.body);
        const out = [];
        out.push('html class = "' + document.documentElement.className + '"');
        out.push('body text ' + body.color + ' on ' + body.backgroundColor);
        out.push('--color-ink-900 = ' + root.getPropertyValue('--color-ink-900').trim());
        out.push('--color-ink-600 = ' + root.getPropertyValue('--color-ink-600').trim());
        out.push('--color-surface = ' + root.getPropertyValue('--color-surface').trim());

        for (const pair of [['card', '.card'], ['h1', 'h1'], ['nav-link', '.nav-link'], ['btn-secondary', '.btn-secondary']]) {
            const el = document.querySelector(pair[1]);
            out.push(el ? pair[0] + ' text ' + getComputedStyle(el).color + ' on ' + getComputedStyle(el).backgroundColor
                : pair[0] + ' (missing)');
        }

        return out.join(' | ');
    } catch (e) {
        return 'PROBE ERROR: ' + e.message;
    }
})()`;

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

  await send('Emulation.setDeviceMetricsOverride', { width: 1280, height: 900, deviceScaleFactor: 1, mobile: false }, S);
  await send('Page.enable', {}, S);
  await send('Runtime.enable', {}, S);

  const read = async (label) => {
    const r = await send('Runtime.evaluate', { expression: PROBE, returnByValue: true, awaitPromise: false }, S);
    const out = r?.result?.value ?? r?.exceptionDetails?.text ?? JSON.stringify(r);
    console.log(`\n===== ${label} =====\n${out}`);
  };

  const waitForPage = async () => {
    for (let i = 0; i < 40; i++) {
      const r = await send('Runtime.evaluate', {
        expression: "document.readyState === 'complete' && !!document.querySelector('.card')",
        returnByValue: true,
      }, S);

      if (r?.result?.value === true) {
        return true;
      }

      await sleep(250);
    }

    return false;
  };

  for (const theme of ['light', 'dark']) {
    await send('Page.navigate', { url }, S);
    await sleep(1500);
    await send('Runtime.evaluate', { expression: `localStorage.setItem('ggs.theme', '${theme}')` }, S);
    await send('Page.navigate', { url }, S);

    const ready = await waitForPage();
    await sleep(500);
    await read(`${theme.toUpperCase()}${ready ? '' : ' (page never finished)'}`);

    const metrics = await send('Page.getLayoutMetrics', {}, S);
    const height = Math.min(metrics.cssContentSize.height, 2200);
    const shot = await send('Page.captureScreenshot', {
      format: 'png',
      captureBeyondViewport: true,
      clip: { x: 0, y: 0, width: 1280, height, scale: 1 },
    }, S);
    fs.writeFileSync(`${out}/${name}-${theme}.png`, Buffer.from(shot.data, 'base64'));
    console.log(`saved ${name}-${theme}.png`);

    // Open the bell and report what the DOM actually did.
    const clicked = await send('Runtime.evaluate', {
      expression: `(() => {
          const trigger = document.querySelector('[data-toggle="notifications-panel"]');
          if (!trigger) { return 'no bell trigger found'; }
          trigger.click();
          const panel = document.getElementById('notifications-panel');
          return 'trigger found, panel hidden = ' + panel.classList.contains('hidden')
              + ', body overflow = ' + document.body.classList.contains('overflow-hidden')
              + ', js ready = ' + document.body.dataset.ready;
      })()`,
      returnByValue: true,
    }, S);
    console.log(`bell: ${clicked?.result?.value ?? JSON.stringify(clicked)}`);

    await sleep(600);
    const panelShot = await send('Page.captureScreenshot', {
      format: 'png',
      clip: { x: 700, y: 0, width: 580, height: 620, scale: 1 },
    }, S);
    fs.writeFileSync(`${out}/${name}-${theme}-bell.png`, Buffer.from(panelShot.data, 'base64'));
    console.log(`saved ${name}-${theme}-bell.png`);

    // Clear one and confirm the row and the badge both go.
    const dismissed = await send('Runtime.evaluate', {
      expression: `(async () => {
          const form = document.querySelector('[data-notification-dismiss]');
          if (!form) { return 'no dismiss form'; }

          form.querySelector('button').click();
          await new Promise((r) => setTimeout(r, 1200));

          const badge = document.querySelector('[data-notification-badge]');
          return 'rows left = ' + document.querySelectorAll('[data-notification-item]').length
              + ', badge = "' + (badge.hidden ? '(hidden)' : badge.textContent) + '"';
      })()`,
      returnByValue: true,
      awaitPromise: true,
    }, S);
    console.log(`after one dismiss: ${dismissed?.result?.value ?? JSON.stringify(dismissed)}`);

    const afterShot = await send('Page.captureScreenshot', {
      format: 'png',
      clip: { x: 700, y: 0, width: 580, height: 500, scale: 1 },
    }, S);
    fs.writeFileSync(`${out}/${name}-${theme}-bell-dismissed.png`, Buffer.from(afterShot.data, 'base64'));
  }

  ws.close();
  chrome.kill();
  process.exit(0);
})().catch((e) => { console.error('ERR', e.message); chrome.kill(); process.exit(1); });
