<?php
$_['heading_title'] = 'e-Računi synchronization';

$_['text_extension'] = 'Extensions';
$_['text_success'] = 'e-Računi synchronization settings were saved.';
$_['text_edit'] = 'Prices and inventory from e-Računi';
$_['text_enabled'] = 'Enabled';
$_['text_disabled'] = 'Disabled';
$_['text_connection'] = 'API connection';
$_['text_connection_ready'] = 'Credentials were found in upload/env.php.';
$_['text_connection_missing'] = 'Missing API settings:';
$_['text_sync'] = 'Manual synchronization';
$_['text_sync_help'] = 'Products are matched through the selected field. Unknown codes and empty API responses never modify products.';
$_['text_prices'] = 'Prices';
$_['text_stock'] = 'Inventory';
$_['text_cron'] = 'EasyCron for quantities';
$_['text_cron_help'] = 'Add this URL to EasyCron as an HTTP GET request. The recommended interval is every 15 minutes. The URL contains a secret key; do not publish it.';
$_['text_last_run'] = 'Last run';
$_['text_never'] = 'Never run';
$_['text_available'] = 'Available inventory (on hand minus reserved)';
$_['text_physical'] = 'Physical inventory';
$_['text_price_net'] = 'No, the value is net';
$_['text_price_gross'] = 'Yes, remove API VAT before saving';
$_['text_copy'] = 'Copy URL';
$_['text_copied'] = 'URL copied.';

$_['entry_status'] = 'Module status';
$_['entry_stock_status'] = 'Allow inventory synchronization';
$_['entry_price_status'] = 'Allow price synchronization';
$_['entry_code_field'] = 'Product code match';
$_['entry_price_field'] = 'e-Računi price field';
$_['entry_price_includes_tax'] = 'Selected price includes VAT';
$_['entry_stock_mode'] = 'Quantity type';
$_['entry_warehouse_code'] = 'Warehouse code';
$_['entry_timeout'] = 'API timeout (seconds)';
$_['entry_cron_key'] = 'Secret cron key';
$_['entry_cron_url'] = 'EasyCron URL';

$_['help_code_field'] = 'Use Model for DryZen: OpenCart models 001–013 match the e-Računi product codes.';
$_['help_price_field'] = 'The existing DryZen connector uses grossPrice. retailPrice and purchasePrice are available for differently configured catalogues. Zero prices are always skipped for safety.';
$_['help_price_includes_tax'] = 'OpenCart stores net prices when a tax class is assigned. retailPrice is always converted to net automatically; for other fields enable this option only when they include VAT.';
$_['help_warehouse_code'] = 'Leave blank to total all warehouses. Enter a code only when the shop uses one specific warehouse.';
$_['help_cron_key'] = 'Changing the key immediately invalidates the previous EasyCron URL.';

$_['button_test'] = 'Test connection';
$_['button_sync_stock'] = 'Update quantities';
$_['button_sync_prices'] = 'Update prices';

$_['error_permission'] = 'You do not have permission to modify e-Računi synchronization.';
$_['error_timeout'] = 'Timeout must be between 5 and 120 seconds.';
$_['error_cron_key'] = 'Cron key must be at least 24 characters and may contain letters, digits, _ and -.';
