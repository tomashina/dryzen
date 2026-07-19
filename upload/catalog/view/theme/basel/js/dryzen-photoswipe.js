import PhotoSwipeLightbox from './photoswipe/photoswipe-lightbox.esm.min.js';

const galleries = document.querySelectorAll('.dryzen-product-editorial .dryzen-product-gallery');

galleries.forEach((gallery) => {
  if (gallery.dataset.photoswipeReady === 'true') {
    return;
  }

  gallery.dataset.photoswipeReady = 'true';

  const lightbox = new PhotoSwipeLightbox({
    gallery,
    children: 'a.dryzen-editorial-tile',
    pswpModule: () => import('./photoswipe/photoswipe.esm.min.js'),
    bgOpacity: 1,
    showHideAnimationType: 'zoom',
    initialZoomLevel: 'fit',
    secondaryZoomLevel: 2.25,
    maxZoomLevel: 5,
    wheelToZoom: true,
    clickToCloseNonZoomable: false,
    closeTitle: 'Zatvori galeriju',
    zoomTitle: 'Povećaj fotografiju',
    arrowPrevTitle: 'Prethodna fotografija',
    arrowNextTitle: 'Sljedeća fotografija',
    paddingFn: (viewportSize) => {
      const compact = viewportSize.x < 768;

      return compact
        ? { top: 64, bottom: 32, left: 10, right: 10 }
        : { top: 52, bottom: 42, left: 72, right: 72 };
    },
  });

  lightbox.init();
});
