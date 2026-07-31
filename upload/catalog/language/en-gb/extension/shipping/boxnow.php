<?php
// Text
$_['text_title']       = 'BOX NOW';
$_['text_description'] = 'BOX NOW locker';

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
$_['text_tracking_email_history'] = 'BOX NOW tracking email sent to the customer. Parcel number:';
$_['mail_subject']              = 'Your BOX NOW shipment - %s';
$_['mail_heading']              = 'Shipment tracking information';
$_['mail_greeting']             = 'Hello %s,';
$_['mail_intro']                = 'a BOX NOW shipment has been created for your order #%s.';
$_['mail_tracking_code']        = 'Parcel number';
$_['mail_tracking_status']      = 'Current status';
$_['mail_shipping_method']      = 'Shipping method';
$_['mail_track_button']         = 'Track shipment';
$_['mail_order_button']         = 'View order';
$_['mail_note']                 = 'Tracking may update only after BOX NOW processes the shipment.';
$_['mail_footer']               = 'Kind regards, %s';

// Errors
$_['error_missing_credentials'] = 'BOX NOW Client ID and Client Secret are required to create shipments.';
$_['error_missing_locker']      = 'This order does not have a selected BOX NOW locker.';
$_['error_not_boxnow_order']    = 'This order is not a BOX NOW shipment.';
$_['error_missing_parcel_id']   = 'BOX NOW did not return a parcel number.';
$_['error_missing_customer_email'] = 'This order does not have a valid customer email address.';
$_['error_missing_label_email_recipients'] = 'There are no valid internal email recipients for the BOX NOW label.';
$_['error_invalid_label_pdf']   = 'BOX NOW did not return a valid PDF label.';
