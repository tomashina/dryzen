<?php

require_once dirname(__DIR__) . '/upload/system/library/return_request.php';

function assertReturnRequest($condition, $message) {
	if (!$condition) {
		fwrite(STDERR, "FAIL: " . $message . "\n");
		exit(1);
	}
}

$service = new return_request();
$items = $service->normaliseItems(array(
	array('name' => '  <b>Krema</b> ', 'code' => ' DZ-01 ', 'quantity' => '2', 'price' => '12,50'),
	array('name' => '', 'code' => '', 'quantity' => '', 'price' => '')
));

assertReturnRequest(count($items) === 1, 'Completely empty product rows are ignored.');
assertReturnRequest($items[0]['name'] === 'Krema', 'Product markup is removed.');
assertReturnRequest($items[0]['code'] === 'DZ-01', 'Product codes are trimmed.');
assertReturnRequest($items[0]['quantity'] === 2, 'Quantities are normalised to integers.');
assertReturnRequest($items[0]['price'] === '12.50', 'Decimal commas are normalised.');
assertReturnRequest($service->validateItems($items), 'A valid item row passes validation.');
assertReturnRequest(!$service->validateItems(array(array('name' => '', 'code' => '', 'quantity' => 1, 'price' => ''))), 'An item needs a name or code.');
assertReturnRequest(!$service->validateItems(array(array('name' => 'Krema', 'code' => '', 'quantity' => 0, 'price' => ''))), 'An item needs a positive quantity.');
assertReturnRequest($service->normaliseType('unexpected') === 'withdrawal', 'Unknown request types safely default to withdrawal.');

$encoded = $service->encodeItems($items);
assertReturnRequest($service->decodeItems($encoded) === $items, 'Structured return items survive JSON storage.');
assertReturnRequest($service->decodeItems('{bad json', $items) === $items, 'Legacy fallback items are used for invalid JSON.');
assertReturnRequest($service->decodeItems('[]', $items) === $items, 'Legacy fallback items are used for an empty JSON list.');
assertReturnRequest($service->maskIban('HR12 3456 7890 1234 5678 9') === 'HR12*************6789', 'IBAN is masked in e-mails.');

$labels = array(
	'return_id' => 'Broj zahtjeva', 'request_type' => 'Vrsta', 'type_withdrawal' => 'Raskid',
	'type_return' => 'Povrat', 'submitted_at' => 'Vrijeme', 'invoice_number' => 'Račun',
	'invoice_date' => 'Datum', 'customer' => 'Kupac', 'email' => 'E-mail', 'telephone' => 'Telefon',
	'items' => 'Artikli', 'reason' => 'Razlog', 'comment' => 'Napomena', 'refund_iban' => 'IBAN'
);
$submission = array(
	'request_type' => 'withdrawal', 'submitted_at' => '2026-10-02 12:00:00', 'invoice_number' => 'IR-42',
	'invoice_date' => '2026-10-01', 'firstname' => 'Ana', 'lastname' => 'Anić', 'email' => 'ana@example.test',
	'telephone' => '+385 91 000 0000', 'return_products' => $items, 'reason' => '', 'comment' => 'Molim potvrdu.',
	'refund_iban' => 'HR1234567890123456789'
);
$mail = $service->buildMailText($labels, 42, $submission, 'Zaprimljeno.', 'Hvala.');
assertReturnRequest(strpos($mail, 'IR-42') !== false && strpos($mail, 'Krema / DZ-01 × 2') !== false, 'Confirmation mail contains invoice and item details.');
assertReturnRequest(strpos($mail, 'HR1234567890123456789') === false && strpos($mail, 'HR12') !== false, 'Confirmation mail never exposes the full IBAN.');

$csv = $service->buildCsv(array(array('42', '=HYPERLINK("bad")', 'normal')), array('ID', 'Name', 'Value'));
assertReturnRequest(strpos($csv, "'=HYPERLINK") !== false, 'CSV formula injection is neutralised.');

$controller = file_get_contents(dirname(__DIR__) . '/upload/catalog/controller/account/return.php');
$admin_controller = file_get_contents(dirname(__DIR__) . '/upload/admin/controller/sale/return.php');
assertReturnRequest(strpos($controller, 'hash_equals($session_token, $post_token)') !== false, 'Public form validates a session CSRF token.');
assertReturnRequest(substr_count($controller, '$this->sendMail(') >= 2, 'Submission sends customer and administrator messages.');
assertReturnRequest(strpos($admin_controller, 'public function export()') !== false, 'Administration provides CSV export.');

echo "Return request tests passed.\n";
