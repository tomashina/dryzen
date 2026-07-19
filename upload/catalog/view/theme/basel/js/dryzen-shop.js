(function () {
  'use strict';

  function clampQuantity(input) {
    var minimum = parseInt(input.getAttribute('min'), 10) || 1;
    var value = parseInt(input.value, 10);

    if (!value || value < minimum) {
      value = minimum;
    }

    input.value = value;

    var card = input.closest('.dryzen-shop-card');
    var minus = card ? card.querySelector('[data-quantity-direction="down"]') : null;

    if (minus) {
      minus.disabled = value <= minimum;
    }

    return value;
  }

  document.addEventListener('click', function (event) {
    var quantityButton = event.target.closest('[data-quantity-direction]');

    if (quantityButton) {
      var quantityCard = quantityButton.closest('.dryzen-shop-card');
      var quantityInput = quantityCard ? quantityCard.querySelector('.dryzen-card-quantity') : null;

      if (!quantityInput) {
        return;
      }

      var current = clampQuantity(quantityInput);
      var direction = quantityButton.getAttribute('data-quantity-direction');
      quantityInput.value = direction === 'up' ? current + 1 : current - 1;
      clampQuantity(quantityInput);
      return;
    }

    var cartButton = event.target.closest('.dryzen-card-cart');

    if (cartButton && !cartButton.disabled) {
      var cartCard = cartButton.closest('.dryzen-shop-card');
      var cartInput = cartCard ? cartCard.querySelector('.dryzen-card-quantity') : null;
      var productId = cartButton.getAttribute('data-product-id');
      var quantity = cartInput ? clampQuantity(cartInput) : 1;

      if (window.cart && typeof window.cart.add === 'function') {
        window.cart.add(productId, quantity);
      }
    }
  });

  document.addEventListener('change', function (event) {
    if (event.target.matches('.dryzen-card-quantity')) {
      clampQuantity(event.target);
    }
  });

  document.querySelectorAll('.dryzen-card-quantity').forEach(clampQuantity);

  function normalizeSlickAccessibility() {
    document.querySelectorAll('.slick-track').forEach(function (track) {
      track.removeAttribute('role');
      track.removeAttribute('aria-label');
    });

    document.querySelectorAll('.slick-slide').forEach(function (slide) {
      slide.removeAttribute('role');
      slide.removeAttribute('aria-describedby');
    });

    document.querySelectorAll('.slick-dots').forEach(function (dots) {
      dots.removeAttribute('role');
    });

    document.querySelectorAll('.slick-dots li').forEach(function (item) {
      item.removeAttribute('role');
      item.removeAttribute('aria-hidden');
      item.removeAttribute('aria-selected');
      item.removeAttribute('aria-controls');
    });

    document.querySelectorAll('.slick-dots button').forEach(function (button, index) {
      button.setAttribute('aria-label', 'Idi na slajd ' + (index + 1));
    });
  }

  document.addEventListener('click', function (event) {
    var menuTrigger = event.target.closest('.menu-trigger');
    var menuCloser = event.target.closest('.menu-closer');

    if (menuTrigger) {
      window.setTimeout(function () {
        menuTrigger.setAttribute(
          'aria-expanded',
          document.body.classList.contains('mobile-menu-open') ? 'true' : 'false'
        );
      }, 0);
    }

    if (menuCloser) {
      var trigger = document.querySelector('.menu-trigger');

      if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
      }
    }
  });

  window.setTimeout(normalizeSlickAccessibility, 600);
  window.setTimeout(normalizeSlickAccessibility, 2400);

  function removeLegacyInlineStyle(style) {
    if (
      style &&
      style.tagName === 'STYLE' &&
      style.textContent.indexOf('.theiaStickySidebar:after') !== -1
    ) {
      style.remove();
    }
  }

  document.querySelectorAll('style').forEach(removeLegacyInlineStyle);

  window.setTimeout(function () {
    document.querySelectorAll('style:empty').forEach(function (style) {
      style.remove();
    });
  }, 2000);

  if (window.MutationObserver) {
    var legacyStyleObserver = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes, removeLegacyInlineStyle);
      });
    });

    legacyStyleObserver.observe(document.head, { childList: true });

    window.setTimeout(function () {
      legacyStyleObserver.disconnect();
    }, 5000);
  }
}());
