<?php
// Croatian   v.2.x.x     Datum: 01.10.2014		Author: Gigo (Igor Ilić - igor@iligsoft.hr)
// Heading
$_['heading_title']			= 'Authorize.Net (SIM)';

// Text
// $_['text_payment']					= 'Plaćanja'; // postojalo u verziji OC 2.2.0.0
$_['text_extension']		= 'Proširenja (extensions)';
$_['text_success']			= 'Uspješno: Napravili ste promjene u Authorize.Net (SIM) korisničkom računu!';
$_['text_edit']             = 'Izmjeni Authorize.Net (SIM)';
$_['text_authorizenet_sim']	= '<a onclick="window.open(\'http://reseller.authorize.net/application/?id=27254\');"><img src="view/image/payment/authorizenet.png" alt="Authorize.Net" title="Authorize.Net" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant']		= 'ID trgovca (merchant)';
$_['entry_key']				= 'Transakcijski ključ';
$_['entry_callback']		= 'URL za slanje odgovora';
$_['entry_md5']				= 'MD5 hash vrijednost';
$_['entry_test']			= 'Test mod';
$_['entry_total']			= 'Ukupno';
$_['entry_order_status']	= 'Status narudžbe';
$_['entry_geo_zone']		= 'Geo zona';
$_['entry_status']			= 'Status'; 
$_['entry_sort_order']		= 'Redoslijed sortiranja';

// Help
$_['help_callback']			= 'Molim prijavite se i postavite ovo na <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>.';
$_['help_md5']				= 'MD5 hash značajka omogućuje Vam da se izvrši provjera/autentifikacija da je odgovor koji dobijete kod transakcije sigurno poslan/dostavljen od Authorize.Net. Molim prijavite se i postavite ovo na <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>.(Opcionalno/nije obavezno, ali je preporučeno)';
$_['help_total']			= 'Minimalna ukupna suma koja mora biti na narudžbi prije nego što ovaj način plaćanja postane aktivan.';

// Error
$_['error_permission']		= 'Upozorenje: Nemate ovlasti da mijenjate Authorize.Net (AIM) korisnički račun!';
$_['error_merchant']		= 'ID trgovca (merchant) je obavezan podatak!';
$_['error_key']				= 'Transakcijski ključ je obavezan podatak!';