/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

// color picker
$.fn.mzColorPicker = function(){
        // input element
        var color_input = $(this).find('input');
        
        // color indicator badge
        if($(this).data('color-badge')){
            var color_badge_target_el = $($(this).data('color-badge'));
        } else {
            var color_badge_target_el = color_input.siblings('.mz-colorpicker-badge');
        }
        color_badge_target_el.css('background-color', color_input.val());
        
        // @link http://www.eyecon.ro/colorpicker/
        color_input.ColorPicker({
                color: color_input.val(),
                onChange: function(hsb, hex, rgb){
                    color_input.val('#' + hex);
                    color_badge_target_el.css('background-color', '#' + hex);
                }
        });
        
        color_input.on('input change', function(){
                color_input.ColorPickerSetColor(this.value);
                color_badge_target_el.css('background-color', this.value);
        });
        
};

function InitJsGlobal(html){
        var html = $(html);
        // color picker
        html.find('.mz-colorpicker').each(function(){
            $(this).mzColorPicker();
        });
        
        // list group tab
        html.find('.list-group > a[data-toggle="tab"]').on('shown.bs.tab', function(event){
            $(event.relatedTarget).removeClass('active');
            $(event.target).addClass('active');
        });
        
        // custom tab
        html.find('.mz-tab-toggle').click(function () {
            $(this).tab('show');
        });
        
        // input image widget
        html.find('.input-image-widget').each(function(){
                var meta_tab_content  =  $(this).find('.image-meta-tab-content');
                
                $(this).find('[data-toggle="tab"]').on('shown.bs.tab', function(e){
                    meta_tab_content.children('.active').removeClass('active');
                    meta_tab_content.children('.tab-' + $(this).data('meta')).addClass('active');
                });
        });
        
        // Link type list
//        html.find('.link-type-list').each(function(){
//            var tab_content = $(this).find('.tab-content');
//            var toggle = $(this).find('[data-toggle="link_type"]');
//            
//            toggle.on('change', function(){
//                var url_type = $(this).val();
//                tab_content.children('.tab-pane.active').removeClass('active');
//                $('#' + tab_content.data('prefix') + '-link-type-' + url_type).addClass('active');
//            });
//            
//            // enable active tab
//            $('#' + tab_content.data('prefix') + '-link-type-' + toggle.val()).addClass('active');
//        });
        
        return html;
}

$(document).ready(function() {
        // Set last page opened on the menu
//	$('#mz-menu a[href]').on('click', function() {
//		sessionStorage.setItem('mz-menu', $(this).attr('href'));
//	});
        
        // Sets active and open to selected page in the left column menu.
//        $('#mz-menu a[href=\'' + sessionStorage.getItem('mz-menu') + '\']').parent().addClass('active');
        
        
        // Color scheme
        $('.color-scheme-option').on('click', function(){
            $(this).find('input[type=radio]').prop('checked', true);
            $(this).closest('.color-scheme').find('.color-scheme-option').removeClass('checked');
            $(this).addClass('checked');
        });
        
        
        
        // toggle-accordion
        $('.toggle-accordion').on('click', function(){
            var accordion_parent = $(this).data('parent');
            $(accordion_parent + ' .collapse.in').collapse('hide');
        });
        
        // Active first tab
        $('.tab-dynamic').each(function(){
            $(this).children('li:first-child').children().tab('show');
        });
        
        // Init Global JS
        InitJsGlobal(document);
        
        // SVG Image Manager
	$(document).on('click', 'a[data-toggle=\'svg\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover'); // element has bs popover?

		e.preventDefault();

		// destroy all image popovers
		$('a[data-toggle="svg"]').popover('destroy');

		// remove flickering (do not re-add popover when clicking for removal)
		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
			var $button = $(this);
			var $icon   = $button.find('> i');

			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=extension/maza/common/svgmanager&user_token=' + getURLVar('user_token') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$button.prop('disabled', true);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
					}
				},
				complete: function() {
					$button.prop('disabled', false);

					if ($icon.length) {
						$icon.attr('class', 'fa fa-pencil');
					}
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$element.popover('destroy');
		});

		$('#button-clear').on('click', function() {
			$element.find('img').attr('src', $element.find('img').attr('data-placeholder'));

			$element.parent().find('input').val('');

			$element.popover('destroy');
		});
	});
        
        // Font icon Manager
	$(document).on('click', 'a[data-toggle=\'font\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover'); // element has bs popover?

		e.preventDefault();

		// destroy all image popovers
		$('a[data-toggle="font"]').popover('destroy');

		// remove flickering (do not re-add popover when clicking for removal)
		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
			var $button = $(this);
			var $icon   = $button.find('> i');

			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=extension/maza/common/font_icon_manager&user_token=' + getURLVar('user_token') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$button.prop('disabled', true);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
					}
				},
				complete: function() {
					$button.prop('disabled', false);

					if ($icon.length) {
						$icon.attr('class', 'fa fa-pencil');
					}
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$element.popover('destroy');
		});

		$('#button-clear').on('click', function() {
			$element.find('i').attr('class', $element.find('i').attr('data-placeholder'));

			$element.parent().find('input').val('');

			$element.popover('destroy');
		});
	});
        
});
        

