<?php

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit('This script can only be run from the command line.');
}

$root = dirname(__DIR__);

function legalGuaranteeFail($message) {
	fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
	exit(1);
}

function legalGuaranteeRead($path) {
	$content = file_get_contents($path);

	if ($content === false) {
		legalGuaranteeFail('Unable to read ' . $path);
	}

	return $content;
}

function legalGuaranteeContains($content, $needle, $message) {
	if (strpos($content, $needle) === false) {
		legalGuaranteeFail($message);
	}
}

$notice_path = $root . '/upload/image/catalog/legal/eu-legal-guarantee-hr.png';

if (!is_file($notice_path)) {
	legalGuaranteeFail('The official Croatian legal-guarantee notice is missing.');
}

if (hash_file('sha256', $notice_path) !== '4731df6c6cb59750cb02d80f4c3d83b8756e60661ec446927853c84f4e9dcd1a') {
	legalGuaranteeFail('The official notice was modified.');
}

$image = getimagesize($notice_path);

if (!$image || $image[0] !== 1654 || $image[1] !== 2339 || $image['mime'] !== 'image/png') {
	legalGuaranteeFail('The official notice has unexpected dimensions or format.');
}

$header_controller = legalGuaranteeRead($root . '/upload/catalog/controller/common/header.php');
legalGuaranteeContains($header_controller, "DIR_IMAGE . 'catalog/legal/eu-legal-guarantee-hr.png'", 'Header does not expose the official notice.');
legalGuaranteeContains($header_controller, 'public function legalGuaranteeImage()', 'Header does not provide the official notice endpoint.');
legalGuaranteeContains($header_controller, 'public function legalGuaranteeStylesheet()', 'Header does not provide the legal-guarantee stylesheet endpoint.');
legalGuaranteeContains($header_controller, 'https://europa.eu/youreurope/jamstva_hr', 'Header does not expose the official EU information URL.');

foreach (array('basel', 'default') as $theme) {
	$header = legalGuaranteeRead($root . '/upload/catalog/view/theme/' . $theme . '/template/common/header.twig');
	legalGuaranteeContains($header, 'id="dryzen-legal-guarantee-modal"', 'The ' . $theme . ' header is missing the notice modal.');
	legalGuaranteeContains($header, 'class="dryzen-legal-guarantee-notice"', 'The ' . $theme . ' header is missing the complete notice image.');
	legalGuaranteeContains($header, 'text_legal_guarantee_full_size', 'The ' . $theme . ' header is missing the full-size notice link.');
	legalGuaranteeContains($header, "$(document).on('click', 'a.dryzen-legal-guarantee-trigger'", 'The ' . $theme . ' header is missing the safe delegated modal handler.');
	if (preg_match('/dryzen-legal-guarantee-trigger[^>]+data-(?:toggle|target)=/', $header)) {
		legalGuaranteeFail('The ' . $theme . ' trigger still asks Bootstrap to load the PNG as remote modal HTML.');
	}
	if (strpos($header, 'dryzen-legal-guarantee-bar') !== false) {
		legalGuaranteeFail('The ' . $theme . ' theme still places the guarantee bar above the header.');
	}

	$footer = legalGuaranteeRead($root . '/upload/catalog/view/theme/' . $theme . '/template/common/footer.twig');
	legalGuaranteeContains($footer, 'class="dryzen-prefooter-guarantee"', 'The ' . $theme . ' theme is missing the guarantee notice between content and footer.');
	legalGuaranteeContains($footer, 'text_footer_withdrawal', 'The ' . $theme . ' footer is missing the withdrawal link.');
	legalGuaranteeContains($footer, 'text_footer_pricelists', 'The ' . $theme . ' footer is missing the price-list link.');

	$checkout = legalGuaranteeRead($root . '/upload/catalog/view/theme/' . $theme . '/template/checkout/confirm.twig');
	legalGuaranteeContains($checkout, 'text_legal_guarantee_link', 'The ' . $theme . ' checkout is missing the legal-guarantee link.');
	legalGuaranteeContains($checkout, 'text_withdrawal_rights_copy', 'The ' . $theme . ' checkout is missing the separate withdrawal notice.');
}

$quick_checkout = legalGuaranteeRead($root . '/upload/catalog/view/theme/basel/template/extension/quickcheckout/confirm.twig');
legalGuaranteeContains($quick_checkout, 'text_legal_guarantee_link', 'Quick checkout is missing the legal-guarantee link.');
legalGuaranteeContains($quick_checkout, 'text_withdrawal_rights_copy', 'Quick checkout is missing the separate withdrawal notice.');

if (strpos($quick_checkout, 'text_order_rights_copy') !== false) {
	legalGuaranteeFail('Quick checkout still uses the old combined rights notice.');
}

$mail_controller = legalGuaranteeRead($root . '/upload/catalog/controller/mail/order.php');
$mail_template = legalGuaranteeRead($root . '/upload/catalog/view/theme/default/template/mail/order_add.twig');
legalGuaranteeContains($mail_controller, 'legal_guarantee_image', 'Order mail does not receive the official notice URL.');
legalGuaranteeContains($mail_controller, 'addAttachment($legal_notice)', 'Order mail does not attach the original official notice.');
legalGuaranteeContains($mail_controller, 'withdrawal_rights_url', 'Order mail does not receive the withdrawal URL.');
legalGuaranteeContains($mail_template, '{{ legal_guarantee_image }}', 'Order mail is missing the full-colour notice.');
legalGuaranteeContains($mail_template, '{{ legal_guarantee_eu_url }}', 'Order mail is missing the official EU information link.');
legalGuaranteeContains($mail_template, '{{ withdrawal_rights_url }}', 'Order mail is missing the separate withdrawal link.');

$notice_css = legalGuaranteeRead($root . '/upload/catalog/view/theme/basel/stylesheet/dryzen-legal-guarantee.css');
legalGuaranteeContains($notice_css, 'overflow-x: auto', 'The mobile notice cannot be panned at a readable size.');
legalGuaranteeContains($notice_css, 'width: 900px', 'The mobile notice is still reduced to an illegible full-page thumbnail.');

foreach (array('hr-hr', 'en-gb') as $language) {
	$checkout_language = legalGuaranteeRead($root . '/upload/catalog/language/' . $language . '/checkout/checkout.php');
	$mail_language = legalGuaranteeRead($root . '/upload/catalog/language/' . $language . '/mail/order_add.php');
	legalGuaranteeContains($checkout_language, "text_legal_guarantee_link", 'Checkout guarantee copy is missing for ' . $language . '.');
	legalGuaranteeContains($checkout_language, "text_withdrawal_rights_copy", 'Checkout withdrawal copy is missing for ' . $language . '.');
	legalGuaranteeContains($mail_language, "text_legal_guarantee_heading", 'Mail guarantee copy is missing for ' . $language . '.');
	legalGuaranteeContains($mail_language, "text_withdrawal_heading", 'Mail withdrawal copy is missing for ' . $language . '.');
}

echo "Legal-guarantee checks passed." . PHP_EOL;
