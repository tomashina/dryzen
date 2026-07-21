(function () {
  'use strict';

  window.gkMetaEventQueue = window.gkMetaEventQueue || [];

  function readAjaxResponse(xhr) {
    var response = xhr && xhr.responseJSON ? xhr.responseJSON : null;

    if (!response && xhr && xhr.responseText) {
      var contentType = xhr.getResponseHeader ? (xhr.getResponseHeader('Content-Type') || '') : '';
      var text = xhr.responseText.replace(/^\s+/, '');

      if (contentType.indexOf('json') !== -1 || text.charAt(0) === '{') {
        try {
          response = JSON.parse(xhr.responseText);
        } catch (error) {
          response = null;
        }
      }
    }

    return response;
  }

  function trackOrQueueMetaEvent(payload) {
    if (!payload || !payload.event) return;

    if (window.gkMarketingConsentGranted && typeof window.fbq === 'function') {
      window.fbq('track', payload.event, payload.data || {});
      return;
    }

    window.gkMetaEventQueue.push(payload);
  }

  function setupMetaEventQueue() {
    if (window.javvMetaPixelAjaxHooked || !window.jQuery) return;

    window.javvMetaPixelAjaxHooked = true;
    window.jQuery(document).ajaxSuccess(function (event, xhr) {
      var response = readAjaxResponse(xhr);

      if (response && response.javv_meta_pixel) {
        trackOrQueueMetaEvent(response.javv_meta_pixel);
      }

      if (response && response.google_analytics_event_encoded &&
          typeof window.gkInjectEncodedMarkup === 'function') {
        window.gkInjectEncodedMarkup(response.google_analytics_event_encoded);
      }
    });
  }

  function flushMetaEvents() {
    if (typeof window.fbq !== 'function') return;

    var pending = window.gkMetaEventQueue.splice(0);
    pending.forEach(function (payload) {
      window.fbq('track', payload.event, payload.data || {});
    });
  }

  function loadMarketingServices() {
    if (window.gkMarketingLoaded) return;

    window.gkMarketingConsentGranted = true;

    if (typeof window.gkLoadMetaPixel === 'function' && window.gkLoadMetaPixel()) {
      window.gkMarketingLoaded = true;
      flushMetaEvents();
    }
  }

  function applyConsent() {
    var analytics = CookieConsent.acceptedCategory('analytics');
    var marketing = CookieConsent.acceptedCategory('marketing');

    window.gkMarketingConsentGranted = marketing;

    if (typeof window.gkSetGoogleConsent === 'function') {
      window.gkSetGoogleConsent(analytics, marketing);
    }

    if (analytics && typeof window.gkLoadAnalytics === 'function') {
      window.gkLoadAnalytics();
    }

    if (marketing) {
      loadMarketingServices();
    }
  }

  function boot() {
    if (!window.CookieConsent || window.gkCookieConsentStarted) return;

    window.gkCookieConsentStarted = true;
    setupMetaEventQueue();

    var english = document.documentElement.lang && document.documentElement.lang.toLowerCase().indexOf('en') === 0;
    var privacyHref = english ? 'privacy-policy' : 'pravila-privatnosti';

    CookieConsent.run({
      cookie: {name: 'cc_cookie', expiresAfterDays: 182, sameSite: 'Lax'},
      disablePageInteraction: true,
      guiOptions: {
        consentModal: {layout: 'box', position: 'middle center', equalWeightButtons: true, flipButtons: false},
        preferencesModal: {layout: 'box', position: 'middle center'}
      },
      categories: {
        necessary: {enabled: true, readOnly: true},
        analytics: {
          enabled: false,
          autoClear: {cookies: [{name: /^_ga/}, {name: '_gid'}, {name: '_gat'}]}
        },
        marketing: {
          enabled: false,
          autoClear: {cookies: [{name: '_fbp'}, {name: '_fbc'}, {name: /^_gcl/}]}
        }
      },
      onFirstConsent: applyConsent,
      onConsent: applyConsent,
      onChange: function () {
        applyConsent();

        if ((window.gkAnalyticsLoaded && !CookieConsent.acceptedCategory('analytics')) ||
            (window.gkMarketingLoaded && !CookieConsent.acceptedCategory('marketing'))) {
          window.location.reload();
        }
      },
      language: {
        default: english ? 'en' : 'hr',
        translations: {
          hr: {
            consentModal: {
              title: 'Vaša privatnost, vaš izbor',
              description: 'Nužni kolačići omogućuju rad trgovine. Uz vaše dopuštenje koristimo Google Analytics za poboljšanje stranice i Meta Pixel za mjerenje oglasa. <a href="' + privacyHref + '">Pravila privatnosti</a>.',
              acceptAllBtn: 'Prihvati sve',
              acceptNecessaryBtn: 'Samo nužni',
              showPreferencesBtn: 'Postavke'
            },
            preferencesModal: {
              title: 'Postavke kolačića',
              acceptAllBtn: 'Prihvati sve',
              acceptNecessaryBtn: 'Samo nužni',
              savePreferencesBtn: 'Spremi odabir',
              closeIconLabel: 'Zatvori',
              sections: [
                {title: 'O vašem izboru', description: 'Izbor možete promijeniti u bilo kojem trenutku poveznicom „Postavke kolačića” u podnožju.'},
                {title: 'Nužni kolačići', description: 'Omogućuju košaricu, prijavu, jezik, valutu, sigurnost i pamćenje vašeg izbora. Ne mogu se isključiti.', linkedCategory: 'necessary'},
                {title: 'Analitika', description: 'Google Analytics pomaže nam razumjeti korištenje trgovine i poboljšati sadržaj. Učitava se tek nakon pristanka.', linkedCategory: 'analytics'},
                {title: 'Marketing', description: 'Meta Pixel mjeri učinak oglasa i događaje PageView, AddToCart i Purchase. Učitava se tek nakon pristanka.', linkedCategory: 'marketing'}
              ]
            }
          },
          en: {
            consentModal: {
              title: 'Your privacy, your choice',
              description: 'Necessary cookies keep the shop working. With your permission we use Google Analytics to improve the site and Meta Pixel to measure advertising. <a href="' + privacyHref + '">Privacy policy</a>.',
              acceptAllBtn: 'Accept all',
              acceptNecessaryBtn: 'Necessary only',
              showPreferencesBtn: 'Settings'
            },
            preferencesModal: {
              title: 'Cookie settings',
              acceptAllBtn: 'Accept all',
              acceptNecessaryBtn: 'Necessary only',
              savePreferencesBtn: 'Save selection',
              closeIconLabel: 'Close',
              sections: [
                {title: 'About your choice', description: 'You can change your choice at any time using the “Cookie settings” link in the footer.'},
                {title: 'Necessary cookies', description: 'Required for the cart, login, language, currency, security and saving your choice.', linkedCategory: 'necessary'},
                {title: 'Analytics', description: 'Google Analytics helps us understand and improve the shop. It loads only after consent.', linkedCategory: 'analytics'},
                {title: 'Marketing', description: 'Meta Pixel measures advertising and the PageView, AddToCart and Purchase events. It loads only after consent.', linkedCategory: 'marketing'}
              ]
            }
          }
        }
      }
    });
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-cookie-consent-trigger]');
    if (!trigger) return;

    event.preventDefault();
    if (window.CookieConsent) CookieConsent.showPreferences();
  });

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
}());
