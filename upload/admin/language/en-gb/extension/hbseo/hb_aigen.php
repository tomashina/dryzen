<?php
// Heading
$_['heading_title']     = 'AI Generator';
$_['text_extension']    = 'SEO Extensions';

// Success Messages
$_['text_success']      = 'Success: Changes updated successfully!';

// Tabs
$_['tab_product']       = 'Products';
$_['tab_category']      = 'Categories';
$_['tab_manufacturer']  = 'Manufacturers';
$_['tab_information']   = 'Information';
$_['tab_items']         = 'AI-Generated Items';
$_['tab_setting']       = 'Settings'; 
$_['tab_logs']          = 'Logs';

// Buttons
$_['button_doc']                = 'Docs';
$_['button_generate']           = 'Generate';
$_['button_generate_selected']  = 'Generate Selected';

$_['button_restore']            = 'Restore';
$_['button_restore_selected']   = 'Restore Selected';
$_['button_restore_all']        = 'Restore All';

$_['button_accept']             = 'Accept';
$_['button_accept_selected']    = 'Accept Selected';
$_['button_accept_all']         = 'Accept All';

$_['button_delete']             = 'Delete';
$_['button_delete_selected']    = 'Delete Selected';
$_['button_delete_all']         = 'Delete All';

$_['button_prompt_preview']     = 'Prompt Preview';

// Columns
$_['column_id']                 = 'ID';
$_['column_type']               = 'Type';
$_['column_item_id']            = 'Item ID';
$_['column_element']            = 'Element';
$_['column_language']           = 'Language';
$_['column_value']              = 'AI-Generated Value';
$_['column_previous_value']     = 'Previous Value';
$_['column_date_added']         = 'Date Added';

$_['column_name']               = 'Name';
$_['column_model']              = 'Model';
$_['column_meta_title']         = 'Meta Title';
$_['column_meta_description']   = 'Meta Description';
$_['column_meta_keyword']       = 'Meta Keyword';
$_['column_action']             = 'Action';

// Text
$_['text_search_product']       = 'Search Product by ID, Name, or Model';
$_['text_search_category']      = 'Search Category by ID or Name';
$_['text_search_manufacturer']  = 'Search Manufacturer by ID or Name';
$_['text_search_information']   = 'Search Information by ID or Title';
$_['text_search_items']         = 'Search Items by ID, Type, Value, Previous Value, or Element';

$_['text_status']               = 'Status';
$_['text_enable_logs']          = 'Enable Logs';
$_['text_api']                  = 'ChatGPT API';
$_['text_api_help']             = 'Enter the ChatGPT API key here. You can get it from <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>.';
$_['text_gpt_model']            = 'GPT model';
$_['text_gpt_max_tokens']       = 'Maximum number of tokens per request';
$_['text_language']             = 'Default Language';
$_['text_cron_key']             = 'CRON Key';
$_['text_cron_limit']           = 'CRON Limit';
$_['text_cron_limit_help']      = 'Number of products processed per CRON run.';
$_['text_cron_command']         = 'CRON Command';
$_['text_description_max_length'] = 'Prompt Description Max Length';
$_['text_simulate']             = 'Simulation Mode. Values are stored in a separate table and not updated in the main database. You can save them manually later.';
$_['text_prompt_template']      = 'Enter the template for the ChatGPT prompt.';

$_['text_description']          = 'Description';
$_['text_meta_title']           = 'Meta Title';
$_['text_meta_description']     = 'Meta Description';
$_['text_meta_keyword']         = 'Meta Keyword';
$_['text_h1']                   = 'H1';
$_['text_h2']                   = 'H2';
$_['text_product_tags']         = 'Product Tags';
$_['text_sections']             = 'Sections';
$_['text_preview']              = 'Prompt Preview';

$_['text_product_prompt_template']       = 'Product Prompt Template';
$_['text_product_prompt_template_help']  = 'You can use the following variables:<br>{name}, {model}, {description}';

$_['text_category_prompt_template']      = 'Category Prompt Template';
$_['text_category_prompt_template_help'] = 'You can use the following variables:<br>{name}, {description}';

$_['text_manufacturer_prompt_template']  = 'Manufacturer Prompt Template';
$_['text_manufacturer_prompt_template_help'] = 'You can use the following variable:<br>{name}';

$_['text_information_prompt_template']   = 'Information Prompt Template';
$_['text_information_prompt_template_help'] = 'You can use the following variables:<br>{title}, {description}';

$_['text_one_language']        = 'Generate only for the default language.';
$_['text_overwrite']           = 'Overwrite existing values.';
$_['text_restore']             = 'Restore all values.';
$_['text_restore_confirm']     = 'Are you sure you want to restore all values?';

$_['text_confirm_generate']    = 'Are you sure you want to generate values for the selected items?';
$_['text_confirm_delete']      = 'Are you sure you want to delete the selected items?';

$_['text_no_records']          = 'No records found!';

// Success Messages
$_['success_restore_success']  = 'All values restored successfully!';
$_['text_success_logs']        = 'Success: Logs cleared successfully!';
$_['success_accept']           = 'Success: AI-generated values updated in main tables!';
$_['success_accept_all']       = 'Success: All AI-generated values updated in main tables!';
$_['success_restore']          = 'Success: Values restored successfully!';
$_['success_restore_all']      = 'Success: All values restored successfully!';
$_['success_delete']           = 'Success: Items deleted successfully!';
$_['success_delete_all']       = 'Success: All items deleted successfully!';

// Error Messages
$_['error_permission']         = 'Warning: You do not have permission to modify this extension!';
$_['error_no_record_selected'] = 'Warning: No records selected!';
$_['error_accept_failed']      = 'Warning: Failed to update values! Check the logs for more details.';
$_['error_restore_failed']     = 'Warning: Failed to restore values! Check the logs for more details.';
?>
