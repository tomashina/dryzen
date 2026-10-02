<?php

/**
 * Shared, framework-light helpers for the consumer withdrawal / return form.
 *
 * The class name intentionally follows OpenCart 3's library loader convention
 * for the `return_request` route.
 */
class return_request {
	const TYPE_WITHDRAWAL = 'withdrawal';
	const TYPE_RETURN = 'return';

	public function __construct($registry = null) {
	}

	public function normaliseType($type) {
		return $type === self::TYPE_RETURN ? self::TYPE_RETURN : self::TYPE_WITHDRAWAL;
	}

	public function normaliseItems($items) {
		$normalised = array();

		if (!is_array($items)) {
			return $normalised;
		}

		foreach ($items as $item) {
			if (!is_array($item)) {
				continue;
			}

			$name = $this->limitText(isset($item['name']) ? $item['name'] : '', 255);
			$code = $this->limitText(isset($item['code']) ? $item['code'] : '', 64);
			$quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
			$price = $this->normalisePrice(isset($item['price']) ? $item['price'] : '');

			// Completely empty rows are ignored so the customer can freely add/remove rows.
			if ($name === '' && $code === '' && $quantity === 0 && $price === '') {
				continue;
			}

			$normalised[] = array(
				'name'     => $name,
				'code'     => $code,
				'quantity' => $quantity,
				'price'    => $price
			);
		}

		return $normalised;
	}

	public function validateItems($items) {
		if (!$items) {
			return false;
		}

		foreach ($items as $item) {
			if (($item['name'] === '' && $item['code'] === '') || $item['quantity'] < 1) {
				return false;
			}

			if ($item['price'] !== '' && (!is_numeric($item['price']) || (float)$item['price'] < 0)) {
				return false;
			}
		}

		return true;
	}

	public function encodeItems($items) {
		$json = json_encode(array_values($items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		return $json === false ? '[]' : $json;
	}

	public function decodeItems($json, $fallback = array()) {
		$items = json_decode((string)$json, true);

		if (!is_array($items)) {
			$items = $fallback;
		}

		$items = $this->normaliseItems($items);

		if (!$items && $fallback) {
			$items = $this->normaliseItems($fallback);
		}

		return $items;
	}

	public function maskIban($iban) {
		$iban = strtoupper(preg_replace('/\s+/', '', trim((string)$iban)));

		if ($iban === '') {
			return '';
		}

		if (strlen($iban) <= 8) {
			return str_repeat('*', max(0, strlen($iban) - 4)) . substr($iban, -4);
		}

		return substr($iban, 0, 4) . str_repeat('*', strlen($iban) - 8) . substr($iban, -4);
	}

	public function buildMailText($labels, $request_id, $data, $intro, $footer) {
		$lines = array(
			$intro,
			'',
			$labels['return_id'] . ': ' . (int)$request_id,
			$labels['request_type'] . ': ' . $labels['type_' . $this->normaliseType($data['request_type'])],
			$labels['submitted_at'] . ': ' . $data['submitted_at'],
			$labels['invoice_number'] . ': ' . $data['invoice_number'],
			$labels['invoice_date'] . ': ' . $data['invoice_date'],
			$labels['customer'] . ': ' . $data['firstname'] . ' ' . $data['lastname'],
			$labels['email'] . ': ' . $data['email'],
			$labels['telephone'] . ': ' . $data['telephone'],
			'',
			$labels['items'] . ':'
		);

		foreach ($data['return_products'] as $item) {
			$description = $item['name'];

			if ($item['code'] !== '') {
				$description .= ($description !== '' ? ' / ' : '') . $item['code'];
			}

			$line = '- ' . $description . ' × ' . (int)$item['quantity'];

			if ($item['price'] !== '') {
				$line .= ' (' . $item['price'] . ')';
			}

			$lines[] = $line;
		}

		if (!empty($data['reason'])) {
			$lines[] = '';
			$lines[] = $labels['reason'] . ': ' . $data['reason'];
		}

		if (!empty($data['comment'])) {
			$lines[] = $labels['comment'] . ': ' . trim(strip_tags($data['comment']));
		}

		if (!empty($data['refund_iban'])) {
			$lines[] = $labels['refund_iban'] . ': ' . $this->maskIban($data['refund_iban']);
		}

		$lines[] = '';
		$lines[] = $footer;

		return implode("\n", $lines);
	}

	public function buildCsv($rows, $headers) {
		$stream = fopen('php://temp', 'w+');

		if (!$stream) {
			throw new RuntimeException('Unable to open the CSV output stream.');
		}

		fputcsv($stream, $headers);

		foreach ($rows as $row) {
			$values = array();

			foreach ($row as $value) {
				$values[] = $this->escapeSpreadsheetFormula($value);
			}

			fputcsv($stream, $values);
		}

		rewind($stream);
		$output = stream_get_contents($stream);
		fclose($stream);

		return $output;
	}

	private function normalisePrice($price) {
		$price = trim((string)$price);

		if ($price === '') {
			return '';
		}

		$price = str_replace(array(' ', ','), array('', '.'), $price);

		if (!is_numeric($price)) {
			return $this->limitText($price, 32);
		}

		return number_format((float)$price, 2, '.', '');
	}

	private function limitText($value, $limit) {
		$value = trim(strip_tags((string)$value));

		if (function_exists('utf8_strlen') && function_exists('utf8_substr')) {
			return utf8_strlen($value) > $limit ? utf8_substr($value, 0, $limit) : $value;
		}

		return strlen($value) > $limit ? substr($value, 0, $limit) : $value;
	}

	private function escapeSpreadsheetFormula($value) {
		$value = (string)$value;

		if ($value !== '' && preg_match('/^[=+\-@]/', $value)) {
			return "'" . $value;
		}

		return $value;
	}
}
