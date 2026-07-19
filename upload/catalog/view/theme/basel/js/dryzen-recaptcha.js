(function (window, document) {
  "use strict";

  if (window.dryzenRecaptchaLoaderBound) {
    return;
  }

  window.dryzenRecaptchaLoaderBound = true;

  var loaded = false;
  var observer = null;

  function loadRecaptcha() {
    if (loaded || document.querySelector('script[data-dryzen-recaptcha-api]')) {
      return;
    }

    loaded = true;

    if (observer) {
      observer.disconnect();
    }

    var script = document.createElement("script");
    script.src = "https://www.google.com/recaptcha/api.js";
    script.async = true;
    script.defer = true;
    script.setAttribute("data-dryzen-recaptcha-api", "");
    document.head.appendChild(script);
  }

  function containsRecaptcha(control) {
    var form = control && control.closest ? control.closest("form") : null;
    return Boolean(form && form.querySelector(".g-recaptcha"));
  }

  function initialise() {
    var widgets = Array.prototype.slice.call(document.querySelectorAll(".g-recaptcha"));

    if (!widgets.length) {
      return;
    }

    document.addEventListener("focusin", function (event) {
      if (containsRecaptcha(event.target)) {
        loadRecaptcha();
      }
    }, { once: true });

    document.addEventListener("pointerdown", function (event) {
      if (containsRecaptcha(event.target)) {
        loadRecaptcha();
      }
    }, { once: true, passive: true });

    if ("IntersectionObserver" in window) {
      observer = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; })) {
          loadRecaptcha();
        }
      }, { rootMargin: "150px 0px" });

      widgets.forEach(function (widget) {
        observer.observe(widget);
      });
    } else {
      loadRecaptcha();
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initialise, { once: true });
  } else {
    initialise();
  }
})(window, document);
