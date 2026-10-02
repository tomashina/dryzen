<?php
// Heading
$_['heading_title']          = 'Products';

// Text
$_['text_success']           = 'Success: You have modified products!';
$_['text_list']              = 'Product List';
$_['text_add']               = 'Add Product';
$_['text_edit']              = 'Edit Product';
$_['text_filter']            = 'Filter';
$_['text_plus']              = '+';
$_['text_minus']             = '-';
$_['text_default']           = 'Default';
$_['text_option']            = 'Option';
$_['text_option_value']      = 'Option Value';
$_['text_percent']           = 'Percentage';
$_['text_amount']            = 'Fixed Amount';
$_['text_keyword']           = 'Do not use spaces, instead replace spaces with - and make sure the SEO URL is globally unique.';

// Column
$_['column_name']            = 'Product Name';
$_['column_model']           = 'Model';
$_['column_image']           = 'Image';
$_['column_price']           = 'Price';
$_['column_quantity']        = 'Quantity';
$_['column_status']          = 'Status';
$_['column_action']          = 'Action';

// Entry
$_['entry_name']             = 'Product Name';
$_['entry_description']      = 'Description';
$_['entry_value_proposition'] = 'Value highlight (above price)';
$_['entry_meta_title']       = 'Meta Tag Title';
$_['entry_meta_keyword']     = 'Meta Tag Keywords';
$_['entry_meta_description'] = 'Meta Tag Description';
$_['entry_store']            = 'Stores';
$_['entry_keyword']          = 'Keyword';
$_['entry_model']            = 'Model';
$_['entry_sku']              = 'SKU';
$_['entry_upc']              = 'UPC';
$_['entry_ean']              = 'EAN';
$_['entry_jan']              = 'JAN';
$_['entry_isbn']             = 'ISBN';
$_['entry_mpn']              = 'MPN';
$_['entry_location']         = 'Location';
$_['entry_shipping']         = 'Requires Shipping';
$_['entry_manufacturer']     = 'Manufacturer';
$_['entry_date_available']   = 'Date Available';
$_['entry_quantity']         = 'Quantity';
$_['entry_minimum']          = 'Minimum Quantity';
$_['entry_stock_status']     = 'Out Of Stock Status';
$_['entry_price']            = 'Price';
$_['entry_tax_class']        = 'Tax Class';
$_['entry_points']           = 'Points';
$_['entry_option_points']    = 'Points';
$_['entry_subtract']         = 'Subtract Stock';
$_['entry_weight_class']     = 'Weight Class';
$_['entry_weight']           = 'Weight';
$_['entry_dimension']        = 'Dimensions (L x W x H)';
$_['entry_length_class']     = 'Length Class';
$_['entry_length']           = 'Length';
$_['entry_width']            = 'Width';
$_['entry_height']           = 'Height';
$_['entry_image']            = 'Image';
$_['entry_additional_image'] = 'Additional Images';
$_['entry_customer_group']   = 'Customer Group';
$_['entry_date_start']       = 'Date Start';
$_['entry_date_end']         = 'Date End';
$_['entry_priority']         = 'Priority';
$_['entry_attribute']        = 'Attribute';
$_['entry_attribute_group']  = 'Attribute Group';
$_['entry_text']             = 'Text';
$_['entry_option']           = 'Option';
$_['entry_option_value']     = 'Option Value';
$_['entry_required']         = 'Required';
$_['entry_status']           = 'Status';
$_['entry_sort_order']       = 'Sort Order';
$_['entry_category']         = 'Categories';
$_['entry_filter']           = 'Filters';
$_['entry_download']         = 'Downloads';
$_['entry_related']          = 'Related Products';
$_['entry_tag']              = 'Product Tags';
$_['entry_reward']           = 'Reward Points';
$_['entry_layout']           = 'Layout Override';
$_['entry_recurring']        = 'Recurring Profile';

$_['help_value_proposition']  = 'A short durability or value message shown with an hourglass above the price. Leave empty to hide it.';

// Help
$_['help_sku']               = 'Stock Keeping Unit';
$_['help_upc']               = 'Universal Product Code';
$_['help_ean']               = 'European Article Number';
$_['help_jan']               = 'Japanese Article Number';
$_['help_isbn']              = 'International Standard Book Number';
$_['help_mpn']               = 'Manufacturer Part Number';
$_['help_manufacturer']      = '(Autocomplete)';
$_['help_minimum']           = 'Force a minimum ordered amount';
$_['help_stock_status']      = 'Status shown when a product is out of stock';
$_['help_points']            = 'Number of points needed to buy this item. If you don\'t want this product to be purchased with points leave as 0.';
$_['help_category']          = '(Autocomplete)';
$_['help_filter']            = '(Autocomplete)';
$_['help_download']          = '(Autocomplete)';
$_['help_related']           = '(Autocomplete)';
$_['help_tag']               = 'Comma separated';

// Error
$_['error_warning']          = 'Warning: Please check the form carefully for errors!';
$_['error_permission']       = 'Warning: You do not have permission to modify products!';
$_['error_name']             = 'Product Name must be greater than 1 and less than 255 characters!';
$_['error_meta_title']       = 'Meta Title must be greater than 1 and less than 255 characters!';
$_['error_model']            = 'Product Model must be greater than 1 and less than 64 characters!';
$_['error_keyword']          = 'SEO URL already in use!';
$_['error_unique']           = 'SEO URL must be unique!';

// Reference price / CSV import
$_['text_anchor_prices'] = 'Reference prices';
$_['text_anchor_csv_success'] = 'Success: Reference prices were updated for %d product(s).';
$_['text_anchor_csv_help'] = 'Upload a CSV up to 2 MB. Use product_id, model, SKU or EAN to identify a product. Required value columns are anchor_price and anchor_price_date (YYYY-MM-DD). The template uses a semicolon separator, so decimal commas are supported.';
$_['entry_anchor_price'] = 'Reference price';
$_['entry_anchor_price_date'] = 'Reference date';
$_['help_anchor_price'] = 'Enter the reference price on the same tax basis as the regular OpenCart price. Leave both fields empty to hide it.';
$_['help_anchor_price_date'] = 'Date to which the reference price relates. Required when a reference price is entered.';
$_['button_anchor_csv_import'] = 'Import reference prices from CSV';
$_['button_anchor_csv_template'] = 'Download CSV template';
$_['error_anchor_price'] = 'Reference price must be a non-negative number with up to 4 decimal places.';
$_['error_anchor_price_required'] = 'Enter a reference price or clear the reference date.';
$_['error_anchor_price_date_required'] = 'Reference date is required when a reference price is entered.';
$_['error_anchor_price_date'] = 'Reference date must use YYYY-MM-DD format.';
$_['error_anchor_price_date_future'] = 'Reference date cannot be in the future.';
$_['error_anchor_csv_invalid_request'] = 'Invalid CSV import request.';
$_['error_anchor_csv_upload'] = 'Select a CSV file that was uploaded successfully.';
$_['error_anchor_csv_size'] = 'The CSV file may not exceed 2 MB.';
$_['error_anchor_csv_extension'] = 'The uploaded file must have a .csv extension.';
$_['error_anchor_csv_failed'] = 'Reference price import failed. No products were changed.';
$_['error_anchor_csv_invalid_file'] = 'The CSV file cannot be read.';
$_['error_anchor_csv_empty_file'] = 'The CSV file is empty.';
$_['error_anchor_csv_duplicate_column'] = 'The CSV contains the column "%s" more than once.';
$_['error_anchor_csv_missing_value_columns'] = 'The CSV must contain anchor_price and anchor_price_date columns.';
$_['error_anchor_csv_missing_identifier_column'] = 'The CSV must contain at least one identifier column: product_id, model, sku or ean.';
$_['error_anchor_csv_missing_identifier'] = 'CSV row %d does not contain a product identifier.';
$_['error_anchor_csv_invalid_product_id'] = 'CSV row %d contains an invalid product_id.';
$_['error_anchor_csv_invalid_price'] = 'CSV row %d contains an invalid reference price.';
$_['error_anchor_csv_missing_date'] = 'CSV row %d requires a reference date.';
$_['error_anchor_csv_unexpected_date'] = 'CSV row %d has a date without a reference price.';
$_['error_anchor_csv_invalid_date'] = 'CSV row %d contains an invalid date; use YYYY-MM-DD.';
$_['error_anchor_csv_future_date'] = 'CSV row %d contains a future reference date.';
$_['error_anchor_csv_too_many_rows'] = 'The CSV may contain at most %d data rows.';
$_['error_anchor_csv_no_data'] = 'The CSV does not contain any data rows.';
$_['error_anchor_csv_column_count'] = 'CSV row %d contains more values than the header.';
$_['error_anchor_csv_product_not_found'] = 'CSV row %d: no product matches %s "%s".';
$_['error_anchor_csv_ambiguous_identifier'] = 'CSV row %d: %s "%s" matches more than one product.';
$_['error_anchor_csv_conflicting_identifiers'] = 'CSV row %d contains identifiers that refer to different products.';
$_['error_anchor_csv_duplicate_product'] = 'CSV row %d repeats product ID %d.';
