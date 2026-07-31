<?php
// Text
$_['text_title']       = 'BOX NOW';
$_['text_description'] = 'BOX NOW paketomat';

$_['status_created']            = 'Pošiljka je kreirana i čeka preuzimanje.';
$_['status_new']                = 'Čeka se preuzimanje iz trgovine.';
$_['status_in_transit']         = 'Pošiljka je u dostavi.';
$_['status_final_destination']  = 'Paket se nalazi u pretincu.';
$_['status_delivered']          = 'Paket je preuzet.';
$_['status_returned']           = 'Paket je vraćen pošiljatelju.';
$_['status_expired']            = 'Isteklo je vrijeme preuzimanja i paket je vraćen pošiljatelju.';
$_['status_canceled']           = 'Pošiljka je poništena.';
$_['status_missing']            = 'Pošiljka se pronalazi.';
$_['status_in_progress']        = 'Pošiljka je u procesu isporuke.';
$_['status_wait_for_load']      = 'Paket čeka preuzimanje iz pretinca.';
$_['status_unknown']            = 'BOX NOW status: %s';
$_['status_unavailable']        = 'Status još nije dostupan.';

// Tracking email
$_['text_tracking_email_history'] = 'Kupcu poslan BOX NOW email za praćenje pošiljke. Broj pošiljke:';
$_['mail_subject']              = 'Vaša BOX NOW pošiljka - %s';
$_['mail_heading']              = 'Podaci za praćenje pošiljke';
$_['mail_greeting']             = 'Bok %s,';
$_['mail_intro']                = 'za vašu narudžbu #%s kreirana je BOX NOW pošiljka.';
$_['mail_tracking_code']        = 'Broj pošiljke';
$_['mail_tracking_status']      = 'Trenutni status';
$_['mail_shipping_method']      = 'Način dostave';
$_['mail_track_button']         = 'Prati pošiljku';
$_['mail_order_button']         = 'Pogledaj narudžbu';
$_['mail_note']                 = 'Status se može promijeniti tek nakon što BOX NOW obradi pošiljku.';
$_['mail_footer']               = 'Lijep pozdrav, %s';

// Errors
$_['error_missing_credentials'] = 'BOX NOW Client ID i Client Secret moraju biti upisani za kreiranje pošiljke.';
$_['error_missing_locker']      = 'Narudžba nema odabran BOX NOW paketomat.';
$_['error_not_boxnow_order']    = 'Narudžba nije BOX NOW dostava.';
$_['error_missing_parcel_id']   = 'BOX NOW nije vratio broj pošiljke.';
$_['error_missing_customer_email'] = 'Narudžba nema ispravnu email adresu kupca.';
$_['error_missing_label_email_recipients'] = 'Nema ispravnih internih email adresa za slanje BOX NOW adresnice.';
$_['error_invalid_label_pdf']   = 'BOX NOW nije vratio ispravnu PDF adresnicu.';
