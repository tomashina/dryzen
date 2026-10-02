<?php
class DryzenAnchorPriceCsvException extends RuntimeException {
	private $error_key;
	private $parameters;

	public function __construct($error_key, array $parameters = array()) {
		parent::__construct($error_key);
		$this->error_key = $error_key;
		$this->parameters = $parameters;
	}

	public function getErrorKey() {
		return $this->error_key;
	}

	public function getParameters() {
		return $this->parameters;
	}
}

class DryzenAnchorPriceCsvImporter {
	const MAX_ROWS = 10000;

	private $today;

	public function __construct($today = null) {
		$this->today = $today ?: date('Y-m-d');
	}

	public function parse($filename) {
		if (!is_file($filename) || !is_readable($filename)) {
			throw new DryzenAnchorPriceCsvException('invalid_file');
		}

		$handle = fopen($filename, 'rb');

		if (!$handle) {
			throw new DryzenAnchorPriceCsvException('invalid_file');
		}

		try {
			$first_line = fgets($handle);

			if ($first_line === false) {
				throw new DryzenAnchorPriceCsvException('empty_file');
			}

			$delimiter = $this->detectDelimiter($first_line);
			rewind($handle);
			$header = fgetcsv($handle, 0, $delimiter);

			if (!is_array($header) || !$header) {
				throw new DryzenAnchorPriceCsvException('empty_file');
			}

			$columns = $this->normaliseHeader($header);
			$this->validateHeader($columns);
			$rows = array();
			$line_number = 1;

			while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
				$line_number++;

				if ($this->isEmptyRow($values)) {
					continue;
				}

				if (count($values) > count($columns)) {
					$extra = array_slice($values, count($columns));

					if (!$this->isEmptyRow($extra)) {
						throw new DryzenAnchorPriceCsvException('column_count', array($line_number));
					}
				}

				$values = array_pad($values, count($columns), '');
				$row = array(
					'line' => $line_number,
					'identifiers' => array(),
					'anchor_price' => '',
					'anchor_price_date' => ''
				);

				foreach ($columns as $index => $column) {
					if ($column === '') {
						continue;
					}

					$value = trim((string)$values[$index]);

					if (in_array($column, array('product_id', 'model', 'sku', 'ean'), true)) {
						$row['identifiers'][$column] = $value;
					} elseif ($column === 'anchor_price') {
						$row['anchor_price'] = $value;
					} elseif ($column === 'anchor_price_date') {
						$row['anchor_price_date'] = $value;
					}
				}

				$row = $this->normaliseRow($row);
				$rows[] = $row;

				if (count($rows) > self::MAX_ROWS) {
					throw new DryzenAnchorPriceCsvException('too_many_rows', array(self::MAX_ROWS));
				}
			}

			if (!$rows) {
				throw new DryzenAnchorPriceCsvException('no_data');
			}

			return $rows;
		} finally {
			fclose($handle);
		}
	}

	private function detectDelimiter($line) {
		$commas = substr_count($line, ',');
		$semicolons = substr_count($line, ';');

		return $semicolons > $commas ? ';' : ',';
	}

	private function normaliseHeader(array $header) {
		$aliases = array(
			'product_id' => 'product_id',
			'id' => 'product_id',
			'model' => 'model',
			'sifra' => 'model',
			'sku' => 'sku',
			'ean' => 'ean',
			'barcode' => 'ean',
			'barkod' => 'ean',
			'anchor_price' => 'anchor_price',
			'sidrena_cijena' => 'anchor_price',
			'anchor_price_date' => 'anchor_price_date',
			'reference_date' => 'anchor_price_date',
			'referentni_datum' => 'anchor_price_date'
		);
		$columns = array();
		$seen = array();

		foreach ($header as $index => $name) {
			$name = trim((string)$name);

			if ($index === 0) {
				$name = preg_replace('/^\xEF\xBB\xBF/', '', $name);
			}

			$key = strtolower(str_replace(array(' ', '-'), '_', $name));
			$column = isset($aliases[$key]) ? $aliases[$key] : '';

			if ($column !== '' && isset($seen[$column])) {
				throw new DryzenAnchorPriceCsvException('duplicate_column', array($name));
			}

			if ($column !== '') {
				$seen[$column] = true;
			}

			$columns[] = $column;
		}

		return $columns;
	}

	private function validateHeader(array $columns) {
		if (!in_array('anchor_price', $columns, true) || !in_array('anchor_price_date', $columns, true)) {
			throw new DryzenAnchorPriceCsvException('missing_value_columns');
		}

		if (!array_intersect(array('product_id', 'model', 'sku', 'ean'), $columns)) {
			throw new DryzenAnchorPriceCsvException('missing_identifier_column');
		}
	}

	private function normaliseRow(array $row) {
		$identifiers = array_filter($row['identifiers'], function($value) {
			return $value !== '';
		});

		if (!$identifiers) {
			throw new DryzenAnchorPriceCsvException('missing_identifier', array($row['line']));
		}

		if (isset($identifiers['product_id']) && (!ctype_digit($identifiers['product_id']) || (int)$identifiers['product_id'] < 1)) {
			throw new DryzenAnchorPriceCsvException('invalid_product_id', array($row['line']));
		}

		$price = str_replace(',', '.', str_replace(' ', '', $row['anchor_price']));

		if ($price === '') {
			$price = '0';
		}

		if (!preg_match('/^(?:0|[1-9][0-9]{0,10})(?:\.[0-9]{1,4})?$/', $price)) {
			throw new DryzenAnchorPriceCsvException('invalid_price', array($row['line']));
		}

		$price = (float)$price;
		$date = trim($row['anchor_price_date']);

		if ($price > 0 && $date === '') {
			throw new DryzenAnchorPriceCsvException('missing_date', array($row['line']));
		}

		if ($price <= 0 && $date !== '') {
			throw new DryzenAnchorPriceCsvException('unexpected_date', array($row['line']));
		}

		if ($date !== '') {
			$parsed = DateTime::createFromFormat('!Y-m-d', $date);
			$errors = DateTime::getLastErrors();

			if (!$parsed || ($errors !== false && ($errors['warning_count'] || $errors['error_count'])) || $parsed->format('Y-m-d') !== $date) {
				throw new DryzenAnchorPriceCsvException('invalid_date', array($row['line']));
			}

			if ($date > $this->today) {
				throw new DryzenAnchorPriceCsvException('future_date', array($row['line']));
			}
		}

		$row['identifiers'] = $identifiers;
		$row['anchor_price'] = $price;
		$row['anchor_price_date'] = $date;

		return $row;
	}

	private function isEmptyRow(array $row) {
		foreach ($row as $value) {
			if (trim((string)$value) !== '') {
				return false;
			}
		}

		return true;
	}
}
