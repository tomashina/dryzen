<?php
// Heading
$_['heading_title']             = 'BOX NOW';

// Text
$_['text_extension']            = 'Dostava';
$_['text_success']              = 'Uspješno: BOX NOW postavke su spremljene!';
$_['text_edit']                 = 'Izmjeni BOX NOW dostavu';
$_['text_enabled']              = 'Omogućeno';
$_['text_disabled']             = 'Onemogućeno';
$_['text_all_zones']            = 'Sve zone';
$_['text_none']                 = 'Nema';
$_['text_small']                = 'Mali';
$_['text_medium']               = 'Srednji';
$_['text_large']                = 'Veliki';
$_['text_shipment_created']     = 'BOX NOW pošiljka je kreirana.';
$_['text_shipment_exists']      = 'BOX NOW pošiljka već postoji.';
$_['text_tracking_email_sent']  = 'Tracking email je poslan kupcu.';
$_['text_tracking_email_already_sent'] = 'Tracking email je već poslan kupcu.';
$_['text_tracking_email_history'] = 'Kupcu poslan BOX NOW email za praćenje pošiljke. Broj pošiljke:';

// Entry
$_['entry_api_url']             = 'API URL';
$_['entry_partner_id']          = 'Partner ID';
$_['entry_warehouse_id']        = 'Warehouse ID';
$_['entry_client_id']           = 'Client ID';
$_['entry_client_secret']       = 'Client Secret';
$_['entry_webhook_secret']      = 'Webhook secret';
$_['entry_tracking_url']        = 'Tracking URL';
$_['entry_origin_name']         = 'Kontakt ime pošiljatelja';
$_['entry_origin_email']        = 'Kontakt email pošiljatelja';
$_['entry_origin_phone']        = 'Kontakt telefon pošiljatelja';
$_['entry_order_prefix']        = 'Prefix broja narudžbe';
$_['entry_cost']                = 'Cijena dostave';
$_['entry_free_total']          = 'Besplatno iznad';
$_['entry_compartment_size']    = 'Veličina pretinca';
$_['entry_tax_class']           = 'Porezna stopa';
$_['entry_geo_zone']            = 'Geo zona';
$_['entry_status']              = 'Status';
$_['entry_sort_order']          = 'Redoslijed sortiranja';

// Help
$_['help_webhook_url']          = 'Webhook URL za BOX NOW: %s';
$_['help_tracking_url']         = 'Koristite {parcel} kao mjesto na koje se umeće broj pošiljke.';
$_['help_compartment_size']     = 'Koristi se kao zadana veličina paketa, posebno kod slanja iz APM-a.';

// Button
$_['button_create_shipment']    = 'Kreiraj BOX NOW pošiljku';
$_['button_label']              = 'BOX NOW labela';
$_['button_send_tracking_email'] = 'Pošalji tracking email';
$_['button_track_shipment']     = 'Prati pošiljku';

// Order tracking
$_['text_boxnow_shipment']      = 'BOX NOW pošiljka';
$_['text_tracking_code']        = 'Broj pošiljke';
$_['text_tracking_status']      = 'Status pošiljke';
$_['text_tracking_updated']     = 'Zadnje ažuriranje';
$_['text_tracking_email']       = 'Email kupcu';
$_['text_tracking_email_not_sent'] = 'Nije poslan';
$_['text_tracking_email_sent_at'] = 'Poslan %s';
$_['text_boxnow_not_created']   = 'Pošiljka još nije kreirana.';

// BOX NOW statuses
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
$_['mail_subject']              = 'Vaša pošiljka je poslana - %s';
$_['mail_heading']              = 'Vaša narudžba je poslana';
$_['mail_greeting']             = 'Bok %s,';
$_['mail_intro']                = 'vaša narudžba #%s predana je dostavnoj službi BOX NOW.';
$_['mail_tracking_code']        = 'Broj pošiljke';
$_['mail_tracking_status']      = 'Trenutni status';
$_['mail_shipping_method']      = 'Način dostave';
$_['mail_track_button']         = 'Prati pošiljku';
$_['mail_order_button']         = 'Pogledaj narudžbu';
$_['mail_note']                 = 'Tracking status se može promijeniti tek nakon što BOX NOW obradi pošiljku. Ako link ne prikaže novi status odmah, pokušajte ponovno malo kasnije.';
$_['mail_footer']               = 'Lijep pozdrav, %s';

// Error
$_['error_permission']          = 'Upozorenje: Nemate ovlasti mijenjati BOX NOW dostavu!';
$_['error_missing_credentials'] = 'BOX NOW Client ID i Client Secret moraju biti upisani za kreiranje pošiljke.';
$_['error_missing_locker']      = 'Narudžba nema odabran BOX NOW paketomat.';
$_['error_not_boxnow_order']    = 'Narudžba nije BOX NOW dostava.';
$_['error_missing_parcel_id']   = 'BOX NOW nije vratio broj pošiljke.';
$_['error_missing_customer_email'] = 'Narudžba nema email adresu kupca.';
$_['error_tracking_email_failed'] = 'Pošiljka je spremljena, ali tracking email nije poslan. Pokušajte ponovno iz narudžbe.';
