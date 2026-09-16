(function($) {
  'use strict';

  var attempts = 0;

  function initReviewCarousel() {
    var slider = $('#mod10 .grid-holder.tm_module');

    if (!slider.length || slider.hasClass('slick-initialized')) {
      return;
    }

    if (!$.fn.slick) {
      attempts += 1;

      if (attempts < 20) {
        window.setTimeout(initReviewCarousel, 100);
      }

      return;
    }

    slider.slick({
      adaptiveHeight: false,
      dots: true,
      arrows: false,
      autoplay: true,
      autoplaySpeed: 3000,
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1,
      responsive: [
        {
          breakpoint: 960,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    }).parents('.cm_block_wrapper').addClass('has-testimonials');
  }

  $(initReviewCarousel);
  $(window).on('load', initReviewCarousel);
})(jQuery);
