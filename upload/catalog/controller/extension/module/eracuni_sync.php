<?php
class ControllerExtensionModuleEracuniSync extends Controller {
	public function index() {
		return '';
	}

	/**
	 * EasyCron endpoint. Only stock is scheduled; prices remain a deliberate
	 * manual action in the OpenCart administration.
	 */
	public function cron() {
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
		$json = array('success' => false);
		$status = 200;

		if (!$this->config->get('module_eracuni_sync_status') || !$this->config->get('module_eracuni_sync_stock_status')) {
			$status = 503;
			$json['message'] = 'e-Racuni sinkronizacija zalihe nije ukljucena.';
		} else {
			$expected = (string)$this->config->get('module_eracuni_sync_cron_key');
			$provided = isset($this->request->get['key']) ? (string)$this->request->get['key'] : '';

			if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
				$status = 403;
				$json['message'] = 'Neispravan EasyCron kljuc.';
			} else {
				try {
					$this->load->library('eracuni/synchronizer');
					$json = $this->synchronizer->syncStock();
				} catch (\Throwable $exception) {
					$status = stripos($exception->getMessage(), 'vec pokrenuta') !== false ? 409 : 500;
					$json['message'] = $exception->getMessage();
				}
			}
		}

		$this->response->addHeader('HTTP/1.1 ' . $status . ' ' . $this->statusText($status));
		$this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	private function statusText($status) {
		$text = array(200 => 'OK', 403 => 'Forbidden', 409 => 'Conflict', 500 => 'Internal Server Error', 503 => 'Service Unavailable');
		return isset($text[$status]) ? $text[$status] : 'Error';
	}
}
