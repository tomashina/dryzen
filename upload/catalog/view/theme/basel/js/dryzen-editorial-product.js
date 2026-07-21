(function () {
  'use strict';

  function initGallery(root) {
    var gallery = root.querySelector('.dryzen-product-gallery');
    var controls = root.querySelector('.dryzen-gallery-controls');

    if (!gallery || !controls) {
      return;
    }

    var slides = Array.prototype.slice.call(gallery.querySelectorAll('.dryzen-editorial-tile'));
    var dots = Array.prototype.slice.call(controls.querySelectorAll('.dryzen-gallery-dot'));
    var previous = controls.querySelector('[data-gallery-direction="previous"]');
    var next = controls.querySelector('[data-gallery-direction="next"]');
    var activeIndex = 0;
    var ticking = false;

    if (slides.length < 2) {
      controls.hidden = true;
      return;
    }

    function setActive(index) {
      activeIndex = Math.max(0, Math.min(index, slides.length - 1));

      dots.forEach(function (dot, dotIndex) {
        var isActive = dotIndex === activeIndex;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });

      previous.disabled = activeIndex === 0;
      next.disabled = activeIndex === slides.length - 1;
    }

    function slideTo(index) {
      var targetIndex = Math.max(0, Math.min(index, slides.length - 1));
      gallery.scrollTo({
        left: slides[targetIndex].offsetLeft - gallery.offsetLeft,
        behavior: 'smooth'
      });
      setActive(targetIndex);
    }

    gallery.addEventListener('scroll', function () {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(function () {
        var nearestIndex = 0;
        var nearestDistance = Number.POSITIVE_INFINITY;

        slides.forEach(function (slide, index) {
          var distance = Math.abs(slide.offsetLeft - gallery.offsetLeft - gallery.scrollLeft);
          if (distance < nearestDistance) {
            nearestDistance = distance;
            nearestIndex = index;
          }
        });

        setActive(nearestIndex);
        ticking = false;
      });
    }, { passive: true });

    previous.addEventListener('click', function () {
      slideTo(activeIndex - 1);
    });

    next.addEventListener('click', function () {
      slideTo(activeIndex + 1);
    });

    dots.forEach(function (dot, index) {
      dot.addEventListener('click', function () {
        slideTo(index);
      });
    });

    window.addEventListener('resize', function () {
      setActive(activeIndex);
    }, { passive: true });

    setActive(0);
  }

  function getStickyHeaderHeight() {
    var headerHeight = 0;

    document.querySelectorAll('.sticky-header').forEach(function (header) {
      var headerStyle = window.getComputedStyle(header);
      var headerRect = header.getBoundingClientRect();

      if (headerStyle.display !== 'none' && headerStyle.visibility !== 'hidden') {
        headerHeight = Math.max(headerHeight, headerRect.height, header.offsetHeight || 0);
      }
    });

    return headerHeight;
  }

  function scrollToAccordionItem(item) {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var targetTop = window.pageYOffset + item.getBoundingClientRect().top - Math.ceil(getStickyHeaderHeight()) - 16;

    window.scrollTo({
      top: Math.max(0, targetTop),
      behavior: reduceMotion ? 'auto' : 'smooth'
    });
  }

  function initAccordion(root) {
    root.querySelectorAll('.dryzen-product-accordion details').forEach(function (item) {
      item.addEventListener('toggle', function () {
        if (!item.open) {
          return;
        }

        window.requestAnimationFrame(function () {
          window.requestAnimationFrame(function () {
            scrollToAccordionItem(item);
          });
        });
      });
    });
  }

  function init() {
    document.querySelectorAll('.dryzen-product-editorial').forEach(function (root) {
      initGallery(root);
      initAccordion(root);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
}());
