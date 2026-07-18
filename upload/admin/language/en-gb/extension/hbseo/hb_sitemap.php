<?php
// Heading
$_['heading_title']     = 'SEO XML Sitemap Generator PRO';
$_['text_extension']    = 'SEO Extensions';

// Success Messages
$_['text_success']     	= 'Success: You have updated the changes!';

// Tabs
$_['tab_dashboard']     = 'Dashboard';
$_['tab_setting']       = 'Settings'; 
$_['tab_links']       	= 'Custom Links';
$_['tab_tools']       	= 'Tools'; 

// Buttons
$_['button_sitemap_index']      = 'Open Sitemap Index';
$_['button_generate']           = 'Generate Real Sitemap Files';
$_['button_remove_links']       = 'Remove Links';
$_['button_google_indexed']     = 'Check Google Indexed Pages';
$_['button_add_htaccess']       = 'Add Code to .htaccess File';
$_['button_fix_dates']          = 'Fix Incorrect Dates';
$_['button_import']             = 'Import Links';

// Columns
$_['column_link']           = 'Link';
$_['column_frequency']      = 'Change Frequency';
$_['column_priority']       = 'Priority';
$_['column_date_added']     = 'Date Added';
$_['col_sitemap_file']      = 'Sitemap File';
$_['col_modified']          = 'Last Modified';

// Text
$_['text_sitemap_index']        = 'Sitemap Index File';
$_['text_sitemap_index_info']   = 'This is the sitemap index file. You can submit this file to Google Search Console or Bing Webmaster Tools. No need to submit individual sitemap files.';
$_['text_all_sitemap_links']    = 'All Sitemap Links in the Sitemap Index';
$_['text_real_sitemap_links']   = 'Saved Sitemap Files';
$_['text_real_sitemap_info'] = '<i class="fa fa-info-circle"></i> This extension can create two types of sitemaps:<br><br>
1. <b>Dynamic Sitemap Files <span class="label label-success">[Recommended]</span></b>: These are generated in real-time and always show the latest data. They are ideal for submitting to search engines because they automatically reflect changes to your website.<br><br>
2. <b>Saved (Real) Sitemap Files</b>: These are static versions of the dynamic sitemaps, saved as XML files on the server. They do not update automatically but are useful for special cases, like offline processing or specific tools.<br><br>
<b>Recommendation:</b> Submit the <b>dynamic sitemap index file</b> to search engines for the best results. Use the saved sitemap files only if needed for specific purposes.';

$_['text_code_missing']                = 'Relevant code not detected in your .htaccess file. Please add the code given in your settings tab to your .htaccess file in your web root directory.';
$_['text_no_record']                   = 'No Records Found';
$_['entry_link']                       = 'Enter Link / Search Link';
$_['text_sitemaps_essential']          = 'Essential Sitemaps';
$_['text_sitemaps_extra']              = 'Extra Sitemaps (Optional)';
$_['text_sitemaps_status']             = 'Sitemap Status';
$_['text_product_sitemap']             = 'Product Sitemap';
$_['text_category_sitemap']            = 'Category Sitemap';
$_['text_manufacturer_sitemap']        = 'Manufacturer Sitemap';
$_['text_information_sitemap']         = 'Information Sitemap';
$_['text_misc_custom_links_sitemap']   = 'Misc/Custom Links Sitemap';
$_['text_category_to_product_sitemap'] = 'Category to Product Sitemap';
$_['text_manufacturer_to_product_sitemap'] = 'Manufacturer to Product Sitemap';
$_['text_product_tags_sitemap']        = 'Product Tags Sitemap';
$_['text_journal3_blog']               = 'Journal3 Blog';
$_['text_enable_extension']            = 'Enable Extension / Enable Sitemap Index';
$_['text_number_of_products']          = 'Number of Products per Page';
$_['text_image_resize_dimension']      = 'Image Resize Dimension';
$_['text_xml_formatter']               = 'XML Formatter';
$_['text_xml_formatter_tooltip']       = '[Recommended] Keep this option disabled. Enable this in case of debugging.';
$_['text_shortcode_label']             = 'Short-code:';
$_['text_product_name']                = 'Product Name';
$_['text_image_caption_pattern']       = 'Image Caption Pattern';
$_['text_image_title_pattern']         = 'Image Title Pattern';
$_['text_additional_image_caption_pattern'] = 'Additional Image Caption Pattern';
$_['text_additional_image_title_pattern'] = 'Additional Image Title Pattern';
$_['text_image_settings']              = 'Sitemap Image Settings';
$_['text_htaccess_code']               = 'Add these lines to your .htaccess file';
$_['text_invalid_date_product']        = '%d Product(s) with invalid last modified date found!';
$_['text_invalid_date_category']       = '%d Category(s) with invalid last modified date found!';
$_['text_import_bulk']                 = 'Import bulk links to Custom Links Sitemap';
$_['text_info_import']                 = 'You can input each link on a new line or separate them using the "|" character.';

// Frequency Options
$_['text_freq_monthly'] = 'This page is updated once a month';
$_['text_freq_weekly']  = 'This page is updated once a week';
$_['text_freq_yearly']  = 'This page is updated once a year';
$_['text_freq_daily']   = 'This page is updated daily';
$_['text_freq_hourly']  = 'This page is updated every hour';
$_['text_freq_always']  = 'This page is updated every time';
$_['text_freq_never']   = 'This page remains the same all the time';

// Priority Options
$_['text_priority_100'] = '100% Most Important';
$_['text_priority_90']  = '90% Important';
$_['text_priority_80']  = '80%';
$_['text_priority_70']  = '70%';
$_['text_priority_60']  = '60%';
$_['text_priority_50']  = '50% Average';
$_['text_priority_40']  = '40%';
$_['text_priority_30']  = '30%';
$_['text_priority_20']  = '20%';
$_['text_priority_10']  = '10% Least Important';

// Success Messages
$_['success_add']                = 'Success: You have successfully added the link!';
$_['success_delete']             = '%d link(s) deleted successfully!';
$_['success_fixed_dates']        = 'Success: Incorrect last modified dates updated successfully!';
$_['success_links_added']        = 'Success: Links added to Custom Sitemap Page.';
$_['success_sitemaps_generated'] = 'Sitemaps generated successfully!';

// Error Messages
$_['error_invalid_request']      = 'Invalid request, please try again!';
$_['error_permission']           = 'Warning: You do not have permission to modify this extension!';
$_['error_no_sitemaps']          = 'No sitemap links found in the sitemap index file. Check if the sitemap is enabled under the settings tab of this page.';
$_['error_link']                 = 'Invalid Link!';
$_['error_link_exists']          = 'Link Already Exists!';
$_['error_no_record_selected']   = 'No Record Selected!';
$_['error_no_links']             = 'No links detected!';
$_['error_invalid_links']        = '%d Invalid Link(s) skipped!';
?>
