<?php

class ControllerExtensionModuleMarketingTracking extends Controller {
	public function capture() {
		$this->response->addHeader('Content-Type: application/json');
		$this->response->addHeader('Cache-Control: no-store');

		if (!isset($this->request->server['REQUEST_METHOD']) || $this->request->server['REQUEST_METHOD'] !== 'POST') {
			$this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
			$this->response->addHeader('Allow: POST');
			$this->response->setOutput(json_encode(array('success' => false)));

			return;
		}

		$tracking = new MarketingTracking($this->registry);
		$code = isset($this->request->post['tracking']) ? $this->request->post['tracking'] : null;
		$this->response->setOutput(json_encode(array('success' => $tracking->capture($code))));
	}
}
