<?php
// Croatian   v.2.x.x     Datum: 01.10.2014		Author: Gigo (Igor Ilić - igor@iligsoft.hr)
// Heading
$_['heading_title']      = 'Jednostrani raskid i povrat artikala';

// Text
$_['text_account']       = 'Korisnički račun';
$_['text_return']        = 'Informacije o povratu artikala';
$_['text_return_detail'] = 'Detalji povrata';
$_['text_description']   = 'Ovim obrascem možete poslati nedvosmislenu izjavu o jednostranom raskidu ugovora u zakonskom roku ili zatražiti povrat pojedinih artikala. Nakon slanja odmah ćete primiti potvrdu e-poštom.';
$_['text_withdrawal_notice_title'] = 'Jednostrani raskid ugovora možete prijaviti ovdje u zakonskom roku od 14 dana. Obrazac je dostupan i bez korisničkog računa.';
$_['text_policy_link']    = 'Pročitajte uvjete, rokove, troškove i iznimke za raskid i povrat';
$_['text_request_type']   = 'Vrsta zahtjeva';
$_['text_type_withdrawal'] = 'Jednostrani raskid ugovora';
$_['text_type_return']    = 'Povrat / zamjena / reklamacija artikala';
$_['text_request_type_help'] = 'Za jednostrani raskid nije potrebno navesti razlog. Za ostale povrate odaberite razlog u nastavku.';
$_['text_declaration']    = 'Nedvosmisleno izjavljujem da šaljem ovaj zahtjev za raskid/povrat i potvrđujem da su uneseni podaci točni.';
$_['text_order']         = 'Podaci kupca i računa';
$_['text_product']       = 'Artikli za povrat';
$_['text_reason']        = 'Razlog povrata';
$_['text_message']       = '<p>Zaprimili smo Vaš zahtjev broj <strong>#%s</strong>. Kopiju predanih podataka poslali smo Vam e-poštom.</p><p>Obavijestit ćemo Vas nakon obrade zahtjeva.</p>';
$_['text_message_generic'] = '<p>Zaprimili smo Vaš zahtjev. Kopiju predanih podataka poslali smo Vam e-poštom.</p>';
$_['text_return_id']     = 'Zahtjev broj:';
$_['text_order_id']      = 'Broj računa:';
$_['text_date_ordered']  = 'Datum računa:';
$_['text_status']        = 'Status:';
$_['text_date_added']    = 'Datum dodavanja:';
$_['text_comment']       = 'Komentari uz zahtjev za povrat';
$_['text_history']       = 'Povijest povrata';
$_['text_empty']         = 'Do sad niste napravili niti jedan povrat!';
$_['text_agree']         = 'Pročitao sam i slažem se s <a href="%s" class="agree"><b>%s</b></a>';
$_['text_return_products_title'] = 'Artikli koje vraćate';
$_['mail_return_admin_subject']    = '%s - novi zahtjev za povrat #%s';
$_['mail_return_customer_subject'] = '%s - zaprimili smo zahtjev za povrat #%s';
$_['mail_return_admin_intro']      = 'Zaprimljen je novi zahtjev za povrat putem digitalnog obrasca.';
$_['mail_return_customer_intro']   = 'Zaprimili smo Vaš zahtjev za povrat. U nastavku je kopija podataka koje ste poslali.';
$_['mail_return_customer_footer']  = 'Kontaktirat ćemo Vas nakon obrade zahtjeva.';
$_['mail_return_admin_footer']     = 'Cjeloviti podaci i status dostupni su u administraciji OpenCarta pod Prodaja > Povrati.';
$_['mail_label_return_id']         = 'Broj zahtjeva';
$_['mail_label_request_type']      = 'Vrsta zahtjeva';
$_['mail_label_submitted_at']      = 'Vrijeme slanja';
$_['mail_label_customer']          = 'Kupac';

// Column
$_['column_return_id']   = 'Povrata artikala broj';
$_['column_order_id']    = 'Broj računa';
$_['column_status']      = 'Status';
$_['column_date_added']  = 'Datum dodavanja';
$_['column_customer']    = 'Kupac';
$_['column_product']     = 'Naziv artikla';
$_['column_model']       = 'Model';
$_['column_quantity']    = 'Količina';
$_['column_price']       = 'Cijena';
$_['column_opened']      = 'Otvoren';
$_['column_comment']     = 'Komentar';
$_['column_reason']      = 'Razlog';
$_['column_action']      = 'Akcija';


// Entry
$_['entry_order_id']     = 'Narudžba broj';
$_['entry_date_ordered'] = 'Datum narudžbe';
$_['entry_invoice_number'] = 'Broj računa';
$_['entry_invoice_date']   = 'Datum računa';
$_['entry_firstname']    = 'Ime';
$_['entry_lastname']     = 'Prezime';
$_['entry_email']        = 'E-mail';
$_['entry_telephone']    = 'Telefon';
$_['entry_product']      = 'Naziv artkla';
$_['entry_model']        = 'Model';
$_['entry_product_code'] = 'Šifra artikla';
$_['entry_quantity']     = 'Količina';
$_['entry_price']        = 'Cijena';
$_['entry_reason']       = 'Razlog povrata';
$_['entry_opened']       = 'Artikl je otvoren';
$_['entry_fault_detail'] = 'Napomena';
$_['entry_refund_iban']  = 'IBAN za povrat sredstava';
$_['help_refund_iban']    = 'Nije obavezno. Povrat kartičnog plaćanja obavlja se na isto sredstvo plaćanja.';
$_['button_add_product'] = 'Dodaj artikl';
$_['button_submit_request'] = 'Pošalji zahtjev';
// $_['entry_captcha']      = 'Upišite kod u polje (kućicu) ispod';

// Error
$_['text_error']         = 'Zahtjev za povrat koji ste zatražili nije pronađen!';
$_['error_order_id']     = 'Broj računa je obavezan podatak!';
$_['error_date_ordered'] = 'Datum računa je obavezan podatak!';
$_['error_firstname']    = 'Ime mora sadržavati između 1 i 32 znaka!';
$_['error_lastname']     = 'Prezime mora sadržavati između 1 i 32 znaka!';
$_['error_email']        = 'Čini se da je navedena e-mail adresa neispravna!';
$_['error_telephone']    = 'Telefon mora sadržavati između 3 i 32 znaka!';
$_['error_product']      = 'Naziv artikla mora imati više od 3 i manje od 255 znakova!';
$_['error_model']        = 'Model artikla mora imati više od 3 i manje od 64 znaka!';
$_['error_reason']       = 'Morate odabrati razlog povrata artikla!';
$_['error_return_products'] = 'Unesite barem jedan artikl s nazivom ili šifrom i ispravnom količinom. Cijena, ako je unesena, mora biti broj.';
$_['error_refund_iban']     = 'Uneseni IBAN nije ispravan.';
$_['error_declaration']     = 'Za slanje zahtjeva morate potvrditi nedvosmislenu izjavu.';
$_['error_security']        = 'Sigurnosna provjera obrasca nije uspjela. Osvježite stranicu i pokušajte ponovno.';
$_['error_form']            = 'Provjerite označena polja u obrascu.';
// $_['error_captcha']      = 'Kod za provjeru (verifikaciju) ne odgovara onom sa slike!'; // postojalo u verziji OC 2.0.3.1
$_['error_agree']        = 'Upozorenje: Morate prihvatiti (složiti se s) %s!';
