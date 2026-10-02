<?php
class ControllerExtensionFeedDigitalPricelist extends Controller {
	public function page() {
		if (!$this->config->get('feed_digital_pricelist_status')) {
			return $this->errorResponse(404, 'Digitalni cjenik nije dostupan.');
		}

		$this->load->language('extension/feed/digital_pricelist');
		$this->document->setTitle($this->language->get('heading_title'));

		$data = $this->language->all();
		$data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/feed/digital_pricelist/page', '', true)
			)
		);
		$data['xml_url'] = $this->url->link('extension/feed/digital_pricelist', 'format=xml', true);
		$data['csv_url'] = $this->url->link('extension/feed/digital_pricelist', 'format=csv', true);
		$data['generated_at'] = '';
		$data['archive'] = array();

		try {
			$this->load->library('digital_pricelist/generator');
			$this->generator->ensureDaily();
			$status = $this->generator->getStatus();

			if (!empty($status['available'])) {
				$data['generated_at'] = $status['generated_at'];
			}

			foreach ($this->generator->getArchive() as $snapshot) {
				$data['archive'][] = array(
					'generated_at' => $snapshot['generated_at'],
					'xml_url' => !empty($snapshot['xml_file']) ? $this->url->link('extension/feed/digital_pricelist/archive', 'file=' . rawurlencode($snapshot['xml_file']), true) : '',
					'csv_url' => !empty($snapshot['csv_file']) ? $this->url->link('extension/feed/digital_pricelist/archive', 'file=' . rawurlencode($snapshot['csv_file']), true) : ''
				);
			}
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist page status failed: ' . $exception->getMessage());
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/digital_pricelist', $data));
	}

	public function index() {
		if (!$this->config->get('feed_digital_pricelist_status')) {
			return $this->errorResponse(404, 'Digitalni cjenik nije dostupan.');
		}

		$format = isset($this->request->get['format']) ? strtolower((string)$this->request->get['format']) : 'xml';

		if ($format !== 'xml' && $format !== 'csv') {
			return $this->errorResponse(400, 'Dostupni formati su XML i CSV.');
		}

		$this->load->library('digital_pricelist/generator');

		try {
			$this->generator->ensureDaily();
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist generation failed: ' . $exception->getMessage());

			// A failed daily refresh must not take the last valid legal snapshot down.
			try {
				$this->generator->getCurrentFile($format);
			} catch (\Throwable $missing_exception) {
				return $this->errorResponse(503, 'Digitalni cjenik trenutačno nije dostupan.');
			}
		}

		try {
			$file = $this->generator->getCurrentFile($format);
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist read failed: ' . $exception->getMessage());
			return $this->errorResponse(503, 'Digitalni cjenik trenutačno nije dostupan.');
		}

		$this->serveFile($file, $format);
	}

	public function archive() {
		if (!$this->config->get('feed_digital_pricelist_status')) {
			return $this->errorResponse(404, 'Digitalni cjenik nije dostupan.');
		}

		$name = isset($this->request->get['file']) ? rawurldecode((string)$this->request->get['file']) : '';

		try {
			$this->load->library('digital_pricelist/generator');
			$file = $this->generator->getArchivedFile($name);
			$this->serveFile($file, $file['format']);
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist archive read failed: ' . $exception->getMessage());
			return $this->errorResponse(404, 'Arhivski cjenik nije dostupan.');
		}
	}

	private function serveFile(array $file, $format) {
		$content = file_get_contents($file['path']);

		if ($content === false) {
			return $this->errorResponse(503, 'Digitalni cjenik trenutačno nije dostupan.');
		}

		$etag = '"' . $file['sha256'] . '"';
		$provided_etag = isset($this->request->server['HTTP_IF_NONE_MATCH']) ? trim((string)$this->request->server['HTTP_IF_NONE_MATCH']) : '';

		$this->response->addHeader('X-Content-Type-Options: nosniff');
		$this->response->addHeader('Cache-Control: public, max-age=3600, stale-if-error=86400');
		$this->response->addHeader('ETag: ' . $etag);

		if ($provided_etag !== '' && hash_equals($etag, $provided_etag)) {
			$this->response->addHeader('HTTP/1.1 304 Not Modified');
			$this->response->setOutput('');
			return;
		}

		if (!empty($file['generated_at'])) {
			$generated_timestamp = strtotime($file['generated_at']);

			if ($generated_timestamp !== false) {
				$this->response->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s', $generated_timestamp) . ' GMT');
			}
		}

		$name = isset($file['name']) ? (string)$file['name'] : ('digitalni-cjenik.' . $format);

		if ($format === 'csv') {
			$this->response->addHeader('Content-Type: text/csv; charset=utf-8');
			$this->response->addHeader('Content-Disposition: inline; filename="' . $name . '"');
		} else {
			$this->response->addHeader('Content-Type: application/xml; charset=utf-8');
			$this->response->addHeader('Content-Disposition: inline; filename="' . $name . '"');
		}

		$this->response->addHeader('Content-Length: ' . strlen($content));
		$this->response->setOutput($content);
	}

	/**
	 * Protected endpoint intended for one daily EasyCron/system-cron request.
	 */
	public function cron() {
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
		$this->response->addHeader('X-Content-Type-Options: nosniff');

		if (!$this->config->get('feed_digital_pricelist_status')) {
			return $this->jsonResponse(503, array('success' => false, 'message' => 'Digitalni cjenik nije uključen.'));
		}

		$expected = (string)$this->config->get('feed_digital_pricelist_cron_key');
		$provided = isset($this->request->get['key']) ? (string)$this->request->get['key'] : '';

		if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
			return $this->jsonResponse(403, array('success' => false, 'message' => 'Neispravan cron ključ.'));
		}

		try {
			$this->load->library('digital_pricelist/generator');
			$result = $this->generator->ensureDaily();

			return $this->jsonResponse(200, array(
				'success'       => true,
				'generated'     => !empty($result['generated']),
				'generated_at'  => isset($result['generated_at']) ? $result['generated_at'] : '',
				'product_count' => isset($result['product_count']) ? (int)$result['product_count'] : 0
			));
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist cron failed: ' . $exception->getMessage());
			return $this->jsonResponse(500, array('success' => false, 'message' => 'Generiranje digitalnog cjenika nije uspjelo.'));
		}
	}

	private function errorResponse($status, $message) {
		$this->response->addHeader('HTTP/1.1 ' . (int)$status . ' ' . $this->statusText($status));
		$this->response->addHeader('Content-Type: text/plain; charset=utf-8');
		$this->response->addHeader('Cache-Control: no-store');
		$this->response->addHeader('X-Content-Type-Options: nosniff');
		$this->response->setOutput($message);
	}

	private function jsonResponse($status, array $data) {
		$this->response->addHeader('HTTP/1.1 ' . (int)$status . ' ' . $this->statusText($status));
		$this->response->setOutput(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	private function statusText($status) {
		$text = array(
			200 => 'OK',
			400 => 'Bad Request',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			503 => 'Service Unavailable'
		);

		return isset($text[(int)$status]) ? $text[(int)$status] : 'Error';
	}
}
