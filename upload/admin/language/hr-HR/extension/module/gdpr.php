<?php
// Module
$_['text_gdpr']             = 'GDPR';
$_['text_gdpr_settings']             = 'GDPR postavke';
$_['text_gdpr_report']             = 'GDPR povijest zahtjeva';

// Heading
$_['heading_title']    = 'GDPR - zaštita osobnih podataka';
$_['module_name'] = 'GDPR';

// Buttons etc.
$_['button_save'] = 'Spremi';
$_['button_cancel'] = 'Odustani';

// Entry
$_['entry_admin']      = 'Ulaz za administratore';
$_['entry_status']     = 'Status';
$_['entry_name'] = 'Ime';
$_['entry_message_text'] = 'Neprocitane poruke';
$_['entry_date_start'] = 'Pocetni datum (YYYY-MM-DD)';
$_['entry_date_end'] = 'Završni datum (YYYY-MM-DD)';
$_['entry_status'] = 'Status';

$_['entry_email_footer'] = 'GDPR Email podnožje';
$_['entry_email_header'] = 'GDPR Email zaglavlje';
$_['entry_locations_of_other_data'] = 'Ostale lokacije/servisi gdje se pohranjuju osobni podaci';
$_['entry_locations_of_servers'] = 'Psihicka lokacija servera gdje je smještena internet stranica i ostali podaci.';
$_['entry_max_requests_day'] = 'Maksimalni broj zahtjeva dnevno';
$_['entry_pending_status'] = 'Tekst statusa U cekanju/obradi';
$_['entry_confirmed_status'] = 'Tekst statusa Potvrdeno';
$_['entry_emailed_status'] = 'Tekst statusa Poslan email';
$_['entry_account_deleted_status'] = 'Tekst statusa Racun obrisan';
$_['entry_data_sent'] = 'POdaci poslani';
$_['entry_unpaid'] = 'Tekst Neplaceno';
$_['entry_free'] = 'Tekst Besplatno';
$_['entry_rejected'] = 'Tekst Odbijeno';
$_['entry_fairuse'] = 'Tekst Poštena upotreba';
$_['entry_max_days_process'] = 'Maksimalan broj dana za odgovor';
$_['entry_right_to_be_forgotten'] = 'Omoguci formu za brisanje podataka';

// Help
$_['help_pending_status'] = 'Ime statusa GDPR zahtjeva koji još nisu verificirani od strane korisnika. Taj naziv statusa ce se prikazati u administratorskom izvještaju o GDPR zahtjevima i biti ce povezan sa prošlim zahtjevima svakog korisnika.';
$_['help_confirmed_status'] = 'Ime statusa GDPR zahtjeva koji su potvrdeni od strane korisnika potvrdoom iz emaila ali još uvijek nisu obradeni (email sa izvještajem još uvijek nije poslan). Taj naziv statusa ce se prikazati u administratorskom izvještaju o GDPR zahtjevima i biti ce povezan sa prošlim zahtjevima svakog korisnika.';
$_['help_emailed_status'] = 'Ime statusa GDPR zahtjeva koij su obradeni / završeni (izvještaj je poslan na email korisnika). Taj naziv statusa ce se prikazati u administratorskom izvještaju o GDPR zahtjevima i biti ce povezan sa prošlim zahtjevima svakog korisnika.';
$_['help_account_deleted_status'] = 'Ime statusa GDPR zahtjeva za brisanjem podataka koji su obradeni/završeni. Taj naziv statusa ce se prikazati u administratorskom izvještaju o GDPR zahtjevima.';
$_['help_locations_of_other_data'] = ' Navedite sve ostale lokacije i web usluge na kojima pohranjujete osobne podatke kupca. Primjer: Mailchimp, Google Docs, itd. Te informacije ce biti ukljucene u GDPR izvještaj koji se šalje korisniku.';
$_['help_locations_of_servers'] = 'Navedite sve relevantne podatke o serveru gdje je smještena Vaša internet stranica, npr. zemlja gdje se nalazi server, hosting provider, da li je hosting provider obveznik provedbe GDPR-a, itd.).';
$_['help_max_requests_day'] = 'Broj zahtjeva koji su dozvoljeni po korisniku. Ovo bi trebalo biti podešenao na neki manji broj kako bi se sprijecili SPAM zahtjevi, internet napadi te optimiziralo korištenje samog servera. Preporucena vrijednost ovog polja je 3.';
$_['help_right_to_be_forgotten'] = 'Ovime omogucujete formu \'Pravo na brisanje svih podataka\' kojom korisnik može automatski obrisati svoj korisnicki racun kao i sve osobne podatke ukoliko to drugacije nije moguce';

// Error
$_['error_permission'] = 'Upozorenje: Nemate prava za vršenjem projena u GDPR modulu!';

// Text
$_['text_module']      = 'Moduli';
$_['text_success']     = 'Uspješno ste izvršili promjene u GDPR modulu!';
$_['text_edit']        = 'Uredivanje GDPR modula';

// Added in v1.4
$_['entry_store_policy_acceptance'] = 'Store Policy Acceptance';
$_['entry_forms_are_private'] = 'GDPR Forms Require Login';

$_['help_store_policy_acceptance'] = 'If set to yes, every time customer accepts your terms on the registration page or in the checkout, this will be recorded in the database. IMPORTANT: if you set it to yes please make sure you checkout is working correctly!';
$_['help_forms_are_private'] = 'If set to yes, GDPR request can only be submitted by a logged in customer.';
