<?php
class ControllerExtensionModuleJavvMetaPixel extends Controller {
	public function index() {
		if (!$this->config->get('module_javv_meta_pixel_status')) {
			return '';
		}

		$pixel_id = $this->getPixelId();

		if (!$pixel_id) {
			return '';
		}

		$page_view_status = $this->config->get('module_javv_meta_pixel_page_view_status');
		$debug_status = $this->config->get('module_javv_meta_pixel_debug_status');
		$output = array();

		$output[] = '<!-- Meta Pixel Code -->';
		$output[] = '<script>';
		$output[] = "!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');";
		$output[] = "fbq('init', " . $this->json($pixel_id) . ");";

		if ($page_view_status) {
			$output[] = "fbq('track', 'PageView');";
		}

		$output[] = '</script>';

		if ($page_view_status) {
			$output[] = '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . rawurlencode($pixel_id) . '&amp;ev=PageView&amp;noscript=1" alt="" /></noscript>';
		}

		if ($this->config->get('module_javv_meta_pixel_add_to_cart_status')) {
			$output[] = $this->getAddToCartListener((bool)$debug_status);
		}

		if ($this->config->get('module_javv_meta_pixel_purchase_status')) {
			$purchase_script = $this->getPurchaseScript((bool)$debug_status);

			if ($purchase_script) {
				$output[] = $purchase_script;
			}
		}

		$output[] = '<!-- End Meta Pixel Code -->';

		return implode("\n", $output);
	}

	private function getPixelId() {
		$pixel_id = trim((string)$this->config->get('module_javv_meta_pixel_pixel_id'));

		return preg_match('/^[0-9]{5,}$/', $pixel_id) ? $pixel_id : '';
	}

	private function getAddToCartListener($debug_status) {
		$debug = $debug_status ? 'true' : 'false';

		return <<<HTML
<script>
(function() {
	if (window.javvMetaPixelAjaxHooked) {
		return;
	}

	window.javvMetaPixelAjaxHooked = true;
	var javvMetaPixelDebug = {$debug};

	function javvMetaPixelTrack(payload) {
		if (!payload || !payload.event || typeof window.fbq !== 'function') {
			return;
		}

		window.fbq('track', payload.event, payload.data || {});

		if (javvMetaPixelDebug && window.console && window.console.log) {
			window.console.log('[Meta Pixel]', payload.event, payload.data || {});
		}
	}

	if (!window.jQuery) {
		return;
	}

	window.jQuery(document).ajaxSuccess(function(event, xhr) {
		var response = xhr.responseJSON || null;

		if (!response && xhr.responseText) {
			var contentType = xhr.getResponseHeader ? (xhr.getResponseHeader('Content-Type') || '') : '';
			var looksLikeJson = contentType.indexOf('json') !== -1 || xhr.responseText.replace(/^\\s+/, '').charAt(0) === '{';

			if (looksLikeJson) {
				try {
					response = JSON.parse(xhr.responseText);
				} catch (e) {
					response = null;
				}
			}
		}

		if (response && response.javv_meta_pixel) {
			javvMetaPixelTrack(response.javv_meta_pixel);
		}
	});
})();
</script>
HTML;
	}

	private function getPurchaseScript($debug_status) {
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';

		if ($route !== 'checkout/success' || empty($this->session->data['javv_meta_pixel_order_id'])) {
			return '';
		}

		$order_id = (int)$this->session->data['javv_meta_pixel_order_id'];
		unset($this->session->data['javv_meta_pixel_order_id']);

		if (!$order_id) {
			return '';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			return '';
		}

		$currency_code = !empty($order_info['currency_code']) ? $order_info['currency_code'] : $this->config->get('config_currency');
		$currency_value = isset($order_info['currency_value']) ? $order_info['currency_value'] : '';
		$value = (float)$this->currency->format($order_info['total'], $currency_code, $currency_value, false);
		$products = $this->model_checkout_order->getOrderProducts($order_id);
		$content_ids = array();
		$contents = array();
		$num_items = 0;

		foreach ($products as $product) {
			$product_id = (string)$product['product_id'];
			$quantity = (int)$product['quantity'];
			$item_price = (float)$this->currency->format((float)$product['price'] + (float)$product['tax'], $currency_code, $currency_value, false);

			$content_ids[] = $product_id;
			$num_items += $quantity;
			$contents[] = array(
				'id' => $product_id,
				'quantity' => $quantity,
				'item_price' => round($item_price, 2)
			);
		}

		$payload = array(
			'value' => round($value, 2),
			'currency' => $currency_code,
			'content_type' => 'product',
			'content_ids' => $content_ids,
			'contents' => $contents,
			'num_items' => $num_items,
			'order_id' => (string)$order_id
		);

		$debug = $debug_status ? 'true' : 'false';

		return '<script>(function(){var payload=' . $this->json($payload) . ';if(typeof window.fbq==="function"){window.fbq("track","Purchase",payload,{eventID:"purchase-' . (int)$order_id . '"});}if(' . $debug . '&&window.console&&window.console.log){window.console.log("[Meta Pixel]","Purchase",payload);}})();</script>';
	}

	private function json($data) {
		return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}
}
