<?php
// Heading
$_['heading_title']             = 'BOX NOW';

// Text
$_['text_extension']            = 'Shipping';
$_['text_success']              = 'Success: You have modified BOX NOW shipping!';
$_['text_edit']                 = 'Edit BOX NOW Shipping';
$_['text_enabled']              = 'Enabled';
$_['text_disabled']             = 'Disabled';
$_['text_all_zones']            = 'All Zones';
$_['text_none']                 = 'None';
$_['text_small']                = 'Small';
$_['text_medium']               = 'Medium';
$_['text_large']                = 'Large';
$_['text_shipment_created']     = 'The BOX NOW shipment was created.';
$_['text_shipment_exists']      = 'The BOX NOW shipment already exists.';
$_['text_tracking_email_sent']  = 'The tracking email was sent to the customer.';
$_['text_tracking_email_already_sent'] = 'The tracking email was already sent to the customer.';
$_['text_tracking_email_history'] = 'BOX NOW tracking email sent to the customer. Parcel number:';
$_['text_label_email_sent']     = 'The label was sent to the internal recipients.';
$_['text_label_email_already_sent'] = 'The label was already sent to the internal recipients.';

// Entry
$_['entry_api_url']             = 'API URL';
$_['entry_partner_id']          = 'Partner ID';
$_['entry_warehouse_id']        = 'Warehouse ID';
$_['entry_client_id']           = 'Client ID';
$_['entry_client_secret']       = 'Client Secret';
$_['entry_webhook_secret']      = 'Webhook secret';
$_['entry_tracking_url']        = 'Tracking URL';
$_['entry_origin_name']         = 'Sender contact name';
$_['entry_origin_email']        = 'Sender contact email';
$_['entry_origin_phone']        = 'Sender contact phone';
$_['entry_order_prefix']        = 'Order number prefix';
$_['entry_cost']                = 'Shipping cost';
$_['entry_three_plus_cost']     = 'Cost for 3 or more items';
$_['entry_free_total']          = 'Free over';
$_['entry_compartment_size']    = 'Compartment size';
$_['entry_tax_class']           = 'Tax Class';
$_['entry_geo_zone']            = 'Geo Zone';
$_['entry_status']              = 'Status';
$_['entry_sort_order']          = 'Sort Order';

// Help
$_['help_webhook_url']          = 'BOX NOW webhook URL: %s';
$_['help_tracking_url']         = 'Use {parcel} where the parcel number should be inserted.';
$_['help_three_plus_cost']      = 'Applied when the total quantity in the cart is 3 or more. Leave empty to use the regular cost.';
$_['help_compartment_size']     = 'Used as the default parcel size, especially when shipping from an APM.';

// Button
$_['button_create_shipment']    = 'Create BOX NOW shipment';
$_['button_label']              = 'BOX NOW label';
$_['button_send_tracking_email'] = 'Send tracking email';
$_['button_send_label_email']   = 'Email label';
$_['button_resend_label_email'] = 'Resend label';
$_['button_track_shipment']     = 'Track shipment';

// Order tracking
$_['text_boxnow_shipment']      = 'BOX NOW shipment';
$_['text_tracking_code']        = 'Parcel number';
$_['text_tracking_status']      = 'Shipment status';
$_['text_tracking_updated']     = 'Last updated';
$_['text_tracking_email']       = 'Customer email';
$_['text_tracking_email_not_sent'] = 'Not sent';
$_['text_tracking_email_sent_at'] = 'Sent %s';
$_['text_label_email']          = 'Label email';
$_['text_label_email_not_sent'] = 'Not sent';
$_['text_label_email_sent_at']  = 'Sent %s';
$_['text_boxnow_not_created']   = 'The shipment has not been created yet.';

// BOX NOW statuses
$_['status_created']            = 'The shipment was created and is awaiting pickup.';
$_['status_new']                = 'Awaiting pickup from the store.';
$_['status_in_transit']         = 'The shipment is in transit.';
$_['status_final_destination']  = 'The parcel is in the locker.';
$_['status_delivered']          = 'The parcel was collected.';
$_['status_returned']           = 'The parcel was returned to the sender.';
$_['status_expired']            = 'The collection period expired and the parcel was returned to the sender.';
$_['status_canceled']           = 'The shipment was cancelled.';
$_['status_missing']            = 'The parcel is being located.';
$_['status_in_progress']        = 'The shipment is being delivered.';
$_['status_wait_for_load']      = 'The parcel is waiting to be collected from the locker.';
$_['status_unknown']            = 'BOX NOW status: %s';
$_['status_unavailable']        = 'The status is not available yet.';

// Tracking email
$_['mail_subject']              = 'Your BOX NOW shipment - %s';
$_['mail_heading']              = 'Shipment tracking information';
$_['mail_greeting']             = 'Hello %s,';
$_['mail_intro']                = 'a BOX NOW shipment has been created for your order #%s.';
$_['mail_tracking_code']        = 'Parcel number';
$_['mail_tracking_status']      = 'Current status';
$_['mail_shipping_method']      = 'Shipping method';
$_['mail_track_button']         = 'Track shipment';
$_['mail_order_button']         = 'View order';
$_['mail_note']                 = 'Tracking may update only after BOX NOW processes the shipment. If the link does not show a new status immediately, please try again later.';
$_['mail_footer']               = 'Kind regards, %s';

// Error
$_['error_permission']          = 'Warning: You do not have permission to modify BOX NOW shipping!';
$_['error_missing_credentials'] = 'BOX NOW Client ID and Client Secret are required to create shipments.';
$_['error_missing_locker']      = 'This order does not have a selected BOX NOW locker.';
$_['error_not_boxnow_order']    = 'This order is not a BOX NOW shipment.';
$_['error_missing_parcel_id']   = 'BOX NOW did not return a parcel number.';
$_['error_missing_customer_email'] = 'This order does not have a customer email address.';
$_['error_tracking_email_failed'] = 'The shipment was saved, but the tracking email was not sent. Please retry from the order.';
$_['error_label_email_failed']  = 'The shipment was saved, but the label was not sent to the internal recipients. Please retry from the order.';
$_['error_missing_label_email_recipients'] = 'There are no valid internal email recipients for the BOX NOW label.';
$_['error_invalid_label_pdf']   = 'BOX NOW did not return a valid PDF label.';
