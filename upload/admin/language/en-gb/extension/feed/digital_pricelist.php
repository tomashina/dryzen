<?php
$_['heading_title'] = 'Digital XML/CSV price list';

$_['text_extension'] = 'Extensions';
$_['text_success'] = 'Digital price list settings were saved.';
$_['text_edit'] = 'Public digital price list';
$_['text_enabled'] = 'Enabled';
$_['text_disabled'] = 'Disabled';
$_['text_public_urls'] = 'Public URLs';
$_['text_schedule'] = 'Daily automatic update';
$_['text_schedule_help'] = 'Schedule the protected cron URL on working days before 08:00, for example at 07:30. If cron is missed, the first public request that day safely refreshes the price list.';
$_['text_archive_help'] = 'Every successful generation creates immutable XML and CSV snapshots. The public archive list and downloads remain available for at least 30 days.';
$_['text_object_details'] = 'Sales object and file details';
$_['text_object_help'] = 'These details are included in the prescribed filename together with the storage sequence and publication time.';
$_['text_last_generation'] = 'Last generation';
$_['text_product_count'] = 'Product count';
$_['text_never'] = 'The price list has not been generated yet';
$_['text_generated'] = 'The digital price list was generated successfully.';
$_['text_copy'] = 'Copy';
$_['text_copied'] = 'URL copied.';

$_['entry_status'] = 'Status';
$_['entry_archive_days'] = 'Archive retention (days)';
$_['entry_xml_url'] = 'Public XML URL';
$_['entry_csv_url'] = 'Public CSV URL';
$_['entry_cron_key'] = 'Secret cron key';
$_['entry_cron_url'] = 'Protected daily cron URL';
$_['entry_object_type'] = 'Sales object type';
$_['entry_object_address'] = 'Sales object address';
$_['entry_object_designation'] = 'Sales object designation';
$_['entry_unit'] = 'Default unit of measure';

$_['help_archive_days'] = 'At least 30 days; keep a larger value if required by your business rules.';
$_['help_cron_key'] = 'Changing the key immediately invalidates the previous cron URL. Public XML and CSV URLs do not contain a secret key.';

$_['button_generate'] = 'Generate now';

$_['error_permission'] = 'You do not have permission to modify the digital price list.';
$_['error_method'] = 'Digital price list generation is only allowed through a POST request.';
$_['error_archive_days'] = 'Archive retention must be between 30 and 3650 days.';
$_['error_cron_key'] = 'The cron key must have 24 to 128 characters and may contain letters, digits, _ and -.';
$_['error_object_address'] = 'Enter the sales object address (up to 255 characters).';
$_['error_object_designation'] = 'Enter the sales object designation (up to 64 characters).';
$_['error_unit'] = 'Enter the default unit of measure (up to 16 characters).';
$_['error_generation'] = 'Generation failed. The previous valid version remains available.';
