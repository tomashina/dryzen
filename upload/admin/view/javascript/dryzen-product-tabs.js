(function ($) {
  'use strict';

  function cleanEditorHtml(html) {
    return String(html || '')
      .replace(/<div\b[^>]*>/gi, '')
      .replace(/<\/div>/gi, '')
      .trim();
  }

  function initializeEditor(element) {
    var $element = $(element);

    if ($element.next('.note-editor').length || $element.next('.cke').length) {
      return;
    }

    $element.summernote({
      disableDragAndDrop: true,
      height: 240,
      emptyPara: '',
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link']]
      ]
    });
  }

  function syncEditor(element) {
    var $element = $(element);
    var html = $element.val();

    try {
      html = $element.summernote('code');
    } catch (error) {
      html = $element.val();
    }

    html = cleanEditorHtml(html);
    $element.val(html);

    try {
      $element.summernote('code', html);
    } catch (error) {
      $element.val(html);
    }
  }

  $(function () {
    var $container = $('#dryzen-product-tabs');
    var $template = $('#dryzen-product-tab-template');

    if (!$container.length || !$template.length) {
      return;
    }

    var nextRow = parseInt($container.attr('data-next-row'), 10) || 0;

    $container.find('[data-dryzen-editor]').each(function () {
      initializeEditor(this);
    });

    $('#button-add-dryzen-tab').on('click', function () {
      var markup = $template.html().replace(/__ROW__/g, String(nextRow));
      var $row = $(markup);

      $container.append($row);
      $row.find('[data-dryzen-editor]').each(function () {
        initializeEditor(this);
      });
      $row.find('[data-dryzen-tab-name]').first().trigger('focus');
      nextRow += 1;
      $container.attr('data-next-row', nextRow);
    });

    $container.on('click', '[data-remove-dryzen-tab]', function () {
      var $row = $(this).closest('.dryzen-product-tab-row');

      $row.find('[data-dryzen-editor]').each(function () {
        try {
          $(this).summernote('destroy');
        } catch (error) {
          // The editor may not have been initialized yet.
        }
      });

      $row.remove();
    });

    $container.on('input', '[data-dryzen-tab-name]', function () {
      var label = $.trim($(this).val()) || 'Novi dropdown';
      $(this).closest('.dryzen-product-tab-row').find('.dryzen-product-tab-label').first().text(label);
    });

    $('#form-product').on('submit', function () {
      $('[data-dryzen-editor], textarea[name^="product_description"][name$="[description]"]').each(function () {
        syncEditor(this);
      });
    });
  });
})(window.jQuery);
