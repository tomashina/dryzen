<?php

//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// Heading
$_['heading_title'] = 'LIVEOPENCART: '.$_['module_name'];
$_['text_edit']     = 'Edit '.$_['module_name'].' Module';

// Text
$_['text_module']           = 'Modules';
$_['text_success']          = 'Settings are modified!';
$_['text_content_top']      = 'Content Top';
$_['text_content_bottom']   = 'Content Bottom';
$_['text_column_left']      = 'Column Left';
$_['text_column_right']     = 'Column Right';
$_['text_ro_updated_to']    = 'Module updated to version ';
$_['text_ro_all_options']   = 'All available options';
$_['text_ro_support']       = "Developer: <a href='http://liveopencart.com' target='_blank'>liveopencart.com</a> | Support, questions and suggestions: <a href=\"mailto:support@liveopencart.com\">support@liveopencart.com</a>";
$_['text_ro_clear_options'] = 'Reset options';

// Entry
$_['entry_settings']                  = 'Main settings';
$_['entry_customer_section_settings'] = 'Customer section settings';
$_['entry_additional']                = 'Additional fields';

$_['entry_custom_theme_id']      = 'Custom theme ID';
$_['entry_custom_theme_id_help'] = 'Should be filled only if an original directory of the used theme is renamed or if the name of the theme directory is not unique';

$_['error_xlsx_lib_is_not_found']          = '%s library is not found (it is necessary for import/export features only).';
$_['error_php_excel_is_necessary_for_xls'] = ' (PHPExcel is necessary for importing XLS) ';
//$_['entry_PHPExcel_not_found']        = '<a href="http://liveopencart.com/PHPExcel" target="_blank" title="What is PHPExcel? How to install PHPExcel?">PHPExcel</a> library not installed. File not found: ';
$_['entry_export']             = 'Export';
$_['entry_export_description'] = 'Export file format: XLSX.<br>First line for fields names, next lines for data.';
$_['entry_export_get_file']    = 'Export file';
$_['entry_export_check_all']   = 'Check all';
$_['entry_export_fields']      = 'Export fields:';
$_['entry_import']             = 'Import (old versions)';
$_['entry_import_ok']          = 'Import completed';
$_['entry_import_description'] = '
<b>Import of data exported using old versions of Related Options PRO (lower than 1.1.0) and Related Options 2 (lower than 2.2.0) module.</b><br><br>

Import file format: XLS. Import uses only first sheet for getting data.
<br>First table row should contain fields names (head): product_id, relatedoptions_model, quantity, price, option_id1, option_value_id1, option_id2, option_value_id2, ... (not product_option_id or product_option_value_id).
<br>Next table rows should contain related options data in accordance with fields names in first table row.
<br><br>Products combinations of related options will be replaced if the same will be found in file on import.';

$_['warning_import_old'] = '<b>Old version import</b> (Related Options PRO below 1.1.0 and Related Options 2 below 2.2.0)';

$_['entry_import_nothing_before'] = 'Don\'t delete related options before import';
$_['entry_import_delete_before']  = 'Delete all related options data before import';
$_['entry_import_product_before'] = 'Delete related options data only for products existing in import file';
$_['button_install_xlsx_lib']     = 'Click to install %s automatically';
$_['success_install_xlsx_lib']    = '%s is installed. Please reload the page.';
$_['button_upload']               = 'Import file';
$_['button_upload_help']          = 'import starts immediately, when the file is selected';
$_['entry_server_response']       = 'Server answer:';
$_['entry_import_result']         = 'Processed products / related options';
$_['button_browse_file']          = 'Browse file...';

$_['entry_export_new']             = 'Export';
$_['entry_export_new_fields']      = 'Export fields:';
$_['entry_export_new_get_file']    = 'Export file';
$_['entry_export_new_check_all']   = 'Check all';
$_['entry_export_new_description'] = 'Export file format: XLSX.<br>
Separate sheets for each variant of Related Options combinations.<br>
Separate sheets for related options discounts and specials.<br>
First row for fields names, next rows for data.<br>
';
$_['ro_entry_export_method']                = 'Export method';
$_['ro_entry_export_method_all']            = 'Export all combinations of related options';
$_['ro_entry_export_method_by_product_ids'] = 'Export the data by a range of product ID\'s';
$_['ro_entry_export_method_by_ro_variant']  = 'Export the data by a variant of related options';
$_['ro_entry_start_product_id']             = 'First product id';
$_['ro_entry_end_product_id']               = 'Last product id';
$_['ro_entry_export_by_variant']            = 'Export variant';
$_['ro_entry_min_product_id']               = 'min ID:';
$_['ro_entry_max_product_id']               = 'max ID:';

$_['entry_import_new']                    = 'Import';
$_['entry_import_new_nothing_before']     = 'Don\'t delete related options before import';
$_['entry_import_new_delete_before']      = 'Delete all related options data before import';
$_['entry_import_new_product_before']     = 'Delete related options data only for products existing in import file';
$_['entry_import_new_button_upload']      = 'Import selected file';
$_['entry_import_new_button_upload_help'] = 'import starts immediately, after the file selection';
$_['entry_import_new_ok']                 = 'Import completed';
$_['entry_import_new_server_response']    = 'Server answer:';
$_['entry_import_new_result']             = 'Processed products / related options';
$_['entry_import_new_error_not_uploaded'] = 'the file is not uploaded';
$_['entry_import_new_error_not_found']    = 'column is not found on sheet';
$_['entry_import_new_error_no_ro']        = 'relevant related options combination is not found for ';
$_['entry_import_new_error_skipped']      = 'skipped sheet';
$_['entry_import_new_error_no_data']      = 'no data on sheet';
$_['entry_import_new_error_no_sheets']    = 'sheets with data is not found';
$_['entry_import_new_error']              = 'Error:';
$_['entry_import_new_everything']         = 'Import all the data present in the file';
$_['entry_import_new_only_discounts']     = 'Import only discounts';

$_['entry_import_new_description'] = '
Import file format: XLSX. <br>
Separate sheets for each variant of Related Options combinations (sheet names should start from "RO").<br>
Two additional sheets for related options discounts and specials ("discounts" and "specials").
First row of every sheet should contain fields names (header): product_id, options_values_ids (options ids and option values ids with comma delimiter - option_id:option_value_id, ...), quantity,	model, sku, ean, upc, location, price_prefix, price, defaultselect, defaultselectpriority, weight_prefix, weight, stock_status_id<br>
Next rows should contain related options data in accordance with fields names in first row.<br>
';

$_['entry_update_quantity']                       = 'Recalc product quantity';
$_['entry_update_quantity_help']                  = 'calculate product quantity depends on related options quantity';
$_['entry_stock_control']                         = 'Use quantity control';
$_['entry_stock_control_help']                    = 'prevent adding to cart for product quantity greater than related options quantity';
$_['entry_update_options']                        = 'Update standard options data';
$_['entry_update_options_help']                   = 'update standard OpenCart product options depends on related options';
$_['entry_update_options_remove']                 = 'Update options with removing';
$_['entry_update_options_remove_help']            = 'If option is completely removed from product related options data (\''.$_['module_name'].'\' tab), it will be automatically removed from product options (\'Option\' tab) ';
$_['entry_subtract_stock']                        = 'Set \'Subtract stock\' for options';
$_['entry_subtract_stock_help']                   = 'set the setting \'Subtract stock\' for values of options used in combinations of related options';
$_['text_subtract_stock_from_product']            = 'From product';
$_['text_subtract_stock_from_product_first_time'] = 'From product (only first time)';
$_['entry_required']                              = 'Related options are required';
$_['entry_required_help']                         = 'make related options required to select in the customer section (to add the product to the shopping cart)';
$_['text_required_first_time']                    = 'Yes (only first time, only on first save)';
$_['entry_options_values']                        = 'Option values';
$_['entry_add_related_options']                   = 'Add related options';
$_['entry_related_options_quantity']              = 'Quantity';
$_['entry_ro_version']                            = $_['module_name'].', version';

$_['entry_additional_fields']                   = 'Additional/optional properties and settings for combinations of related options';
$_['text_ro_set_options_variants']              = 'related options variants should be set on the module settings page';
$_['entry_ro_disable_all_options_variant']      = 'Disable variant \''.$_['text_ro_all_options'].'\'';
$_['entry_ro_disable_all_options_variant_help'] = 'disable specific related options variant containing all options compatible with the module (recommended for most of cases)';
$_['entry_ro_use_variants']                     = 'Use different variants of related options';
$_['entry_ro_use_variants_help']                = 'allow to combine different options for different products';
$_['entry_ro_variant']                          = 'Related options variant';
$_['entry_ro_variant_name']                     = 'Variant name';
$_['entry_ro_options']                          = 'Variant options';
$_['entry_ro_sort_order']                       = 'Sort order';
$_['entry_ro_add_variant']                      = 'Add variant';
$_['entry_ro_delete_variant']                   = 'Delete variant';
$_['entry_ro_add_option']                       = 'Add option';
$_['entry_ro_delete_option']                    = 'Delete option';
$_['entry_ro_use']                              = 'Enable related options';
$_['entry_show_clear_options']                  = 'Show \'Reset options\'';
$_['entry_show_clear_options_help']             = 'show button \'Reset options\' on the product page in the customer section (to reset all selected option values by click)';
$_['option_show_clear_options_not']             = 'do not use';
$_['option_show_clear_options_top']             = 'above options';
$_['option_show_clear_options_bot']             = 'below options';
$_['entry_hide_inaccessible']                   = 'Hide unavailable values';
$_['entry_hide_inaccessible_help']              = 'hide unselectable option values from the customers';
$_['entry_hide_option']                         = 'Hide unavailable options';
$_['entry_hide_option_help']                    = 'hide option on the product page in the customer section, if all values of the option are unavailable/unselectable';
$_['entry_unavailable_not_required']            = 'Unavailable option is not required';
$_['entry_unavailable_not_required_help']       = 'make unavailable/unselectable options not required';
$_['entry_spec_model']                          = 'Model';
$_['entry_spec_model_help']                     = 'allow to set different models for combinations of related options (this models will be shown on the product page and ont the cart page, and will be saved in orders data)';
$_['entry_spec_model_0']                        = 'Off';
$_['entry_spec_model_1']                        = 'On';
$_['entry_spec_model_2']                        = 'On, calculate: related options model 1 + related options model 2 + etc';
$_['entry_spec_model_3']                        = 'On, calculate: product model + related options model 1 + related options model 2 + etc';
$_['entry_defaults_to_cart']                    = 'Defaults to cart';
$_['entry_defaults_to_cart_help']               = 'Add default combination of related options to the shopping cart from product lists (when possible)';

$_['entry_spec_model_delimiter_product'] = 'Delimiter product-options';
$_['entry_spec_model_delimiter_ro']      = 'Delimiter options-options';

$_['entry_spec_sku']                    = 'SKU';
$_['entry_spec_sku_help']               = 'allow to set different SKU for combinations of related options (this SKU will be saved in orders data)';
$_['entry_spec_upc']                    = 'UPC';
$_['entry_spec_upc_help']               = 'allow to set different UPC for combinations of related options (this UPC will be saved in orders data)';
$_['entry_spec_ean']                    = 'EAN';
$_['entry_spec_ean_help']               = 'allow to set different EAN for combinations of related options (this EAN will be saved in orders data)';
$_['entry_spec_jan']                    = 'JAN';
$_['entry_spec_jan_help']               = 'allow to set different EAN for combinations of related options (this JAN will be saved in orders data)';
$_['entry_spec_location']               = 'Location';
$_['entry_spec_location_help']          = 'allow to set different Location for combinations of related options (this Location will be saved in orders data)';
$_['entry_spec_price']                  = 'Price';
$_['entry_spec_price_help']             = 'allow to set different prices for combinations of related options, if price for related options are not set - standard product price will be used';
$_['entry_spec_inss']                   = 'In Stock Status';
$_['entry_spec_inss_help']              = 'allow to set different In-Stock statuses for combinations of related options (these In-Stock statuses will be shown on the product page, when selected combinations of related options is in stock)';
$_['entry_spec_ofs']                    = 'Out Of Stock Status';
$_['entry_spec_ofs_help']               = 'allow to set different Out Of Stock statuses or actual stock for combinations of related options (this Out Of Stock statuses will be shown on the product page, when selected combination of related options is out of stock)';
$_['entry_spec_weight']                 = 'Weight';
$_['entry_spec_weight_help']            = 'allow to set different weights for combinations of related options';
$_['entry_spec_price_discount']         = 'Discounts';
$_['entry_spec_price_discount_help']    = 'allow to set different discounts for combinations of related options (works if \''.$_['entry_spec_price'].'\' turned on, if discounts for related options are not set - standard product discounts will be used)';
$_['entry_add_discount']                = 'Add discount';
$_['entry_del_discount_title']          = 'Delete discount';
$_['entry_spec_price_special']          = 'Specials';
$_['entry_spec_price_special_help']     = 'allow to set different specials for combinations of related options (works if \''.$_['entry_spec_price'].'\' turned on, if specials for related options are not set - standard product specials will be used)';
$_['entry_spec_disabled']               = 'Disabled';
$_['entry_spec_disabled_help']          = 'allow to disable/hide particular combinations of related options (so they become unavailable/unselectable for customers)';
$_['entry_add_special']                 = 'Add special';
$_['entry_del_special_title']           = 'Delete special';
$_['entry_prices']                      = 'Price';
$_['entry_select_first_short']          = 'Auto-select';
$_['entry_select_first_priority']       = 'Priority';
$_['entry_select_first']                = 'Select options automatically';
$_['entry_select_first_help']           = 'select related option values automatically (on the product page in the customer section)';
$_['option_select_first_not']           = 'off';
$_['option_select_first']               = 'the first available combination of options (initially, on the product page loading | preffered combinations can be defined)';
$_['option_select_first_last']          = 'if there is only one available option value';
$_['option_select_first_always']        = 'the first available option value (always, on every step of option selection | preffered combinations can be defined)';
$_['option_select_first_of_first']      = 'the first value of the first option (initially, on the product page loading)';
$_['entry_step_by_step']                = 'Step-by-step options selection';
$_['entry_step_by_step_help']           = 'customer selects first option, then second, then third, and next, and next etc. (customer can change value of selected options anytime - all next options with unsuitable values will be cleared)';
$_['entry_pagination']                  = 'Pagination';
$_['entry_pagination_help']             = 'display combinations of related options on the product edit page by parts (pages) - helpful to avoid slowing down in case of having many option combinations per product ';
$_['entry_allow_zero_select']           = 'Allow zero quantities';
$_['entry_allow_zero_select_help']      = 'allow customers to select out of stock combinations of related options (having zero quantity)';
$_['entry_edit_columns']                = 'Related Options editing';
$_['entry_edit_columns_0']              = '1 column';
$_['entry_edit_columns_2']              = '2 columns';
$_['entry_edit_columns_3']              = '3 columns';
$_['entry_edit_columns_4']              = '4 columns';
$_['entry_edit_columns_5']              = '5 columns';
$_['entry_edit_columns_100']            = 'by width';
$_['entry_edit_columns_help']           = 'set position select fields for editing related options (Related Option tab on product editing page';
$_['entry_add_all_variants']            = 'Add all possible combinations';
$_['entry_add_product_variants']        = 'Add all product combinations';
$_['entry_spec_price_prefix']           = "Price prefix";
$_['entry_spec_price_prefix_help']      = "Allow price prefixes '+' or '-' for related options prices";
$_['text_update_alert']                 = '(new version available)';
$_['text_combs_number']                 = 'Number of options combinations ';
$_['text_combs_number_out_of_limit']    = ' is too high. It is impossible to operate this number of combinations per product.';
$_['text_combs_number_is_big']          = ' is high. To generate all of them may take a long time and sometimes even freeze the browser. Do you want to process?';
$_['text_combs_will_be_added']          = ' new combinations of options will be added (to total %s). Continue?';
$_['text_combs_all_exist']              = 'All possible combinations already exist (nothing to add)';
$_['entry_delete_all_combs']            = 'Remove all combinations';
$_['text_delete_all_combs']             = 'All combinations of options will be removed. Continue?';
$_['entry_copy_comb_button']            = 'Allow copy combinations';
$_['entry_copy_comb_button_help']       = 'display the button to copy any combination of options on editing related options';
$_['entry_copy_comb_button_help_title'] = 'copy the current combination of options to the end of the list';
$_['text_used_in_ro_combs']             = 'Used in combinations of related options';
$_['text_confirm_variant_removing']     = 'On removing the variant, will be also removed all linked combinations of related options (%s). Сontinue?';
$_['text_if_any']                       = 'if any';

$_['text_use_global_setting'] = 'Use global settings';

$_['entry_about']        = 'About';
$_['module_description'] = '
The module is designed to create combinations of related product options and set stock, price, model, sku, etc for each combination.<br>
I also allows to limit customers to select only available combinations of related options.
<br>This functionality can be useful for sales of products, having interlinked options, such as size and color for clothes.<br>
PRO module version allows to set some different variants of combinations of related options per product.
<br><br>
';

$_['text_conversation'] = 'We are open for conversation. If you need modify or integrate our modules, add new functionality or develop new modules, email as to <b><a href="mailto:support@liveopencart.com">support@liveopencart.com</a></b>.';

$_['entry_we_recommend'] = 'We also recommend:';
$_['text_we_recommend']  = '';

$_['module_copyright'] = '"'.$_['module_name'].'". is a commercial extension. Resell or transfer it to other users is NOT ALLOWED.
<br>By purchasing this module, you get it for use on one site. 
If you want to use the module on multiple sites, you should purchase a separate copy for each site.
<br>Thank you for purchasing the module.
';

//warning
$_['warning_equal_options']  = 'matching set of options';
$_['warning_max_input_vars'] = 'Warning: there a lot of data on the page (options etc), to save it properly php configuration setting <b>max_input_vars</b> should be increased for admin section directory (current value is %s).';

// Error
$_['error_equal_options']      = 'matching set of options';
$_['error_not_enough_options'] = 'not all related options are set';
$_['error_permission']         = 'Warning: You do not have permission to modify module!';
$_['error_modificaton']        = 'Warning: '.$_['module_name'].' modification (OCMOD) is not applied!';
