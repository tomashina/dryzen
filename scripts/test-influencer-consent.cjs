const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname,
  '../upload/catalog/view/javascript/cookieconsent/dryzen-consent.js'), 'utf8');

// Execute only our consent configuration. CookieConsent, browser services and
// requests are simulated; this test never loads Meta, Google or a real URL.
function scenario({query = '', cookie = '', marketing = false, ready = true} = {}) {
  const requests = [];
  const writes = [];
  const cookies = new Map();
  const listeners = {};
  const consent = {marketing, analytics: false};
  let settings;

  cookie.split(/;\s*/).filter(Boolean).forEach(item => {
    const separator = item.indexOf('=');
    cookies.set(item.slice(0, separator), item.slice(separator + 1));
  });

  const document = {
    readyState: ready ? 'complete' : 'loading',
    baseURI: 'https://dryzen.test/shop/',
    documentElement: {lang: 'hr'},
    addEventListener(event, callback) { listeners[event] = callback; }
  };

  Object.defineProperty(document, 'cookie', {
    get() { return [...cookies].map(([name, value]) => name + '=' + value).join('; '); },
    set(value) {
      writes.push(value);
      const pair = value.split(';')[0];
      const separator = pair.indexOf('=');
      const name = pair.slice(0, separator);
      if (/Max-Age=0/i.test(value)) cookies.delete(name);
      else cookies.set(name, pair.slice(separator + 1));
    }
  });

  const CookieConsent = {
    acceptedCategory(category) { return consent[category] === true; },
    run(configuration) { settings = configuration; },
    showPreferences() {}
  };

  const window = {
    CookieConsent, URL, URLSearchParams,
    location: {search: query, protocol: 'https:', reload() {}},
    // No analytics globals or DOM script-insertion APIs are available here.
    fetch(url, options) {
      return new Promise((resolve, reject) => requests.push({url, options, resolve, reject}));
    }
  };

  vm.runInNewContext(source, {window, document, CookieConsent, URL, URLSearchParams}, {
    filename: 'dryzen-consent.js'
  });

  return {
    requests, writes, consent, cookies, window,
    get settings() { return settings; },
    ready() { listeners.DOMContentLoaded(); }
  };
}

async function main() {
  let state = scenario({query: '?tracking=ana', ready: false});
  assert.equal(state.settings, undefined, 'Wait for the page to be ready');
  state.ready();
  assert.equal(state.requests.length, 0, 'First visit must wait for consent');
  assert.equal(state.settings.categories.marketing.enabled, false);
  assert(state.settings.categories.marketing.autoClear.cookies.some(cookie => cookie.name === 'tracking'));

  state.consent.marketing = true;
  state.settings.onFirstConsent();
  state.settings.onConsent();
  assert.equal(state.requests.length, 1, 'Repeated first-consent callbacks capture once');
  const request = state.requests[0];
  assert.equal(request.options.body, 'tracking=ana');
  assert.equal(request.options.method, 'POST');
  assert.equal(request.options.credentials, 'same-origin');
  assert.equal(request.options.cache, 'no-store');
  assert.equal(new URL(request.url).pathname, '/shop/index.php', 'Respect the storefront base URL');
  assert.equal(new URL(request.url).searchParams.get('route'), 'extension/module/marketing_tracking/capture');

  state = scenario({query: '?tracking=ana', cookie: 'tracking=ana', marketing: true});
  state.settings.onConsent();
  assert.equal(state.requests.length, 0, 'Existing attribution must not be captured again');
  assert.equal(state.writes.length, 0, 'Existing attribution lifetime must not be refreshed by JavaScript');

  state = scenario({query: '?tracking=ana', cookie: 'tracking=old'});
  state.settings.onConsent();
  assert.equal(state.requests.length, 0, 'Declining marketing does not capture');
  assert.equal(state.cookies.has('tracking'), false, 'Declining marketing removes prior attribution');
  assert(state.writes.every(value => /Max-Age=0/.test(value) && /Secure/.test(value)));
  state.consent.marketing = true;
  state.settings.onChange();
  assert.equal(state.requests.length, 1, 'Accepting later on the landing page captures its link');

  state.consent.marketing = false;
  state.settings.onChange();
  const clearsBeforeResponse = state.writes.length;
  state.requests[0].resolve({});
  await Promise.resolve();
  assert(state.writes.length > clearsBeforeResponse, 'Revocation also clears a cookie from an in-flight capture');
  assert.equal(state.cookies.has('tracking'), false);

  for (const query of ['', '?tracking[]=ana', '?tracking[x]=ana', '?tracking=bad%20code',
    '?tracking=%3Cscript%3E', '?tracking=' + 'a'.repeat(65)]) {
    state = scenario({query, marketing: true});
    state.settings.onConsent();
    assert.equal(state.requests.length, 0, 'Reject missing or invalid tracking: ' + query);
  }

  state = scenario({query: '?tracking=first&tracking=last', marketing: true});
  state.settings.onConsent();
  assert.equal(state.requests[0].options.body, 'tracking=last', 'Use the last scalar query parameter');

  state = scenario({query: '?tracking=ana._~-2026', marketing: true});
  state.settings.onConsent();
  assert.equal(state.requests[0].options.body, 'tracking=ana._~-2026', 'Allow URL-safe codes');
  state.requests[0].reject(new Error('Simulated network failure'));
  await Promise.resolve();
  await Promise.resolve();

  console.log('PASS: influencer consent, landing capture, revocation and input validation.');
}

main().catch(error => {
  console.error(error);
  process.exitCode = 1;
});
