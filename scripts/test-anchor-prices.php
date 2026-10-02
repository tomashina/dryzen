<?php

require_once dirname(__DIR__) . '/upload/system/library/anchor_price/csv_importer.php';

function anchorAssert($condition, $message) {
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

function anchorTempCsv($contents) {
	$filename = tempnam(sys_get_temp_dir(), 'dryzen-anchor-');
	file_put_contents($filename, $contents);

	return $filename;
}

function anchorExpectError($contents, $error_key) {
	$filename = anchorTempCsv($contents);

	try {
		$importer = new DryzenAnchorPriceCsvImporter('2026-10-02');
		$importer->parse($filename);
		throw new RuntimeException('Expected CSV error: ' . $error_key);
	} catch (DryzenAnchorPriceCsvException $exception) {
		anchorAssert($exception->getErrorKey() === $error_key, 'Unexpected error key: ' . $exception->getErrorKey());
	} finally {
		unlink($filename);
	}
}

$filename = anchorTempCsv("model;anchor_price;anchor_price_date\nDZ-01;19,9900;2026-09-30\nDZ-02;0;\n");
$importer = new DryzenAnchorPriceCsvImporter('2026-10-02');
$rows = $importer->parse($filename);
unlink($filename);

anchorAssert(count($rows) === 2, 'Expected two parsed rows.');
anchorAssert($rows[0]['identifiers']['model'] === 'DZ-01', 'Model was not parsed.');
anchorAssert(abs($rows[0]['anchor_price'] - 19.99) < 0.00001, 'Decimal comma was not normalised.');
anchorAssert($rows[0]['anchor_price_date'] === '2026-09-30', 'Reference date was not parsed.');
anchorAssert($rows[1]['anchor_price'] === 0.0 && $rows[1]['anchor_price_date'] === '', 'Clear row was not parsed.');

$filename = anchorTempCsv("\xEF\xBB\xBFproduct_id,anchor_price,reference_date\n12,25.50,2026-10-01\n");
$rows = $importer->parse($filename);
unlink($filename);
anchorAssert($rows[0]['identifiers']['product_id'] === '12', 'BOM/product ID handling failed.');

anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;12.00;\n", 'missing_date');
anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;12.00;2026-10-03\n", 'future_date');
anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;0;2026-10-01\n", 'unexpected_date');
anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;-1;2026-10-01\n", 'invalid_price');
anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;12.00;2026-02-30\n", 'invalid_date');
anchorExpectError("model;anchor_price;sidrena_cijena;anchor_price_date\nDZ-01;12.00;12.00;2026-10-01\n", 'duplicate_column');
anchorExpectError("anchor_price;anchor_price_date\n12.00;2026-10-01\n", 'missing_identifier_column');
anchorExpectError("model;anchor_price;anchor_price_date\n;12.00;2026-10-01\n", 'missing_identifier');
anchorExpectError("model;anchor_price;anchor_price_date\nDZ-01;12.12345;2026-10-01\n", 'invalid_price');

echo "Anchor price CSV tests passed.\n";
