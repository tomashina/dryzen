var live_search = {
	selector: '.header-wrapper input[name=\'search\']',
}

$(document).ready(function() {
	var html = '';
	html += '<div class="live-search">';
	html += '<table class="table products">';
	html += '</table>';
	html += '<div class="result-text"></div>';
	html += '</div>';

	//$(live_search.selector).parent().closest('div').after(html);
	$(live_search.selector).after(html);

	$(live_search.selector).autocomplete({
		'source': function(request, response) {
			var filter_name = $(live_search.selector).val();
			if (filter_name.length == 0) {
				$('.live-search').css('display','none');
			}
			else{
				$('.result-text').html();
				var html = '';
				html +=	'<tr><td style="text-align:center;padding:20px 10px 10px"><span class="basel-spinner"></span></td></tr>';
				$('.live-search .table.products').html(html);
				$('.live-search').css('display','block');

				$.ajax({
					url: 'index.php?route=extension/basel/live_search&filter_name=' +  encodeURIComponent(filter_name),
					dataType: 'json',
					type: 'post',
					success: function(result,json) {
						var products = result.products;
						$('.live-search .table.products tr').remove();
						$('.result-text').html('');
						if (!$.isEmptyObject(products)) {
							var show_image = true;
							var show_price = true;
							$('.result-text').html('<a href="'+result.search_url+'&search='+filter_name+'" class="view-all-results">'+result.basel_text_view_all+'('+result.total+')</a>');

							$.each(products, function(index,product) {
								var html = '';
								
								html += '<tr onclick="location.href=\'' + product.url + '\'">';
								if(product.image){
									html += '<td class="image"><img alt="' + product.name + '" src="' + product.image + '"></td>';
									html += '<td class="main">';
								} else {
									html += '<td colspan="2" class="main">';
								}
								html += '<a class="product-name main-font">' + product.name + '</a>';

								if(show_price){
									if (product.special) {
										html += '<div class="price"><span class="price-old">' + product.price + ' <small> ' + product.priceeur + ' </small> </span><span class="price">' + product.special + ' <small> ' + product.specialeur + ' </small></span></div>';
									} else {
										html += '<div class="price"><span class="price">' + product.price + ' <small> ' + product.priceeur + ' </small></span></div>';
									}
								}
								html += '</td>';
								html += '</tr>';
								$('.live-search .table.products').append(html);
							});
						} else {
							var html = '';
							html +=	result.basel_text_no_result;

							$('.result-text').html(html);
						}
						$('.live-search').css('display','block');
						return false;
					}
				});
			}
		},
		'select': function(product) {
			$(live_search.selector).val(product.name);
		}
	});

	$(document).bind( "mouseup touchend", function(e){
	  var container = $('.live-search');
	  if (!container.is(e.target) && container.has(e.target).length === 0)
	  {
		container.hide();
	  }
	});
});