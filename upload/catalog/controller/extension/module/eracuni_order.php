<?php
class ControllerExtensionModuleEracuniOrder extends Controller {
	public function checkoutSuccess(&$route, &$data) {
		if (!$this->config->get('module_eracuni_sync_status') || empty($this->session->data['order_id'])) {
			return;
		}

		$order_id = (int)$this->session->data['order_id'];

		try {
			$this->load->library('eracuni/order_exporter');
			$this->order_exporter->export($order_id);
		} catch (\Throwable $exception) {
			$this->log->write(
				'Automatska izrada e-Računi računa za narudžbu #' . $order_id . ' nije uspjela: ' . $exception->getMessage()
			);
		}
	}
}
