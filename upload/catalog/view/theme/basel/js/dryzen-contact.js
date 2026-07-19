(function ($) {
  'use strict';

  $(function () {
    $('.dryzen-contact-page .to_form').on('click', function () {
      var $form = $('.dryzen-contact-form');

      if ($form.length) {
        $('html, body').animate({
          scrollTop: Math.max(0, $form.offset().top - 120)
        }, 500);
      }
    });
  });
})(window.jQuery);
