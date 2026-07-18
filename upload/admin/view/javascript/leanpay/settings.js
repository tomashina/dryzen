(function ($) {
  $(document).ready(function () {
    var $globalCheckbox = $('#input-global-config');
    var $storeSelect = $('#input-store');
    var currentStore = $storeSelect.val();
    var $storeSelectField = $storeSelect.closest('.form-group');

    if ($globalCheckbox.is(':checked')) {
      $storeSelectField.hide();
    }

    $globalCheckbox.on('change', function () {
      $storeSelectField.toggle();
    });

    $storeSelect.on('change', function () {
      window.location.href = $storeSelect.data('url') + '&store_id=' + $storeSelect.val();
      $storeSelect.val(currentStore);
    });
  })
})(jQuery);
