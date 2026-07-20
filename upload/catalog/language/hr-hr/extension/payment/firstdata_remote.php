<?php
// Croatian   v.2.x.x     Datum: 01.10.2014		Author: Gigo (Igor Ilić - igor@iligsoft.hr)
// Text
$_['text_title']				= 'Kreditnaili debitna kartica';
$_['text_credit_card']			= 'Detalji o kreditnoj kartici';
$_['text_wait']					= 'Pričekajte molim!';

// Entry
$_['entry_cc_number']			= 'Broj kartice';
$_['entry_cc_name']				= 'Vlasnik kartice';
$_['entry_cc_expire_date']		= 'Datum isteka kartice';
$_['entry_cc_cvv2']				= 'Sigurnosni kod kartice (CVV2)';

// Help
$_['help_start_date']			= '(ako je dosutpan)';
$_['help_issue']				= '(samo za Maestro i Solo kartice)';

// Text
$_['text_result']				= 'Rezultat: ';
$_['text_approval_code']		= 'Kod odobrenja: ';
$_['text_reference_number']		= 'Referenca: ';
$_['text_card_number_ref']		= 'Zadnja 4 broja kartice: xxxx ';
$_['text_card_brand']			= 'Marka/naziv kartice: ';
$_['text_response_code']		= 'Kod odgovora: ';
$_['text_fault']				= 'Obavijest o pogrešci: ';
$_['text_error']				= 'Poruka o grešci: ';
$_['text_avs']					= 'Provjera adrese: ';
$_['text_address_ppx']			= 'Nema dostupnih podataka o adresi ili adresa nije provjerena od strane izdavatelja kartice';
$_['text_address_yyy']			= 'Izdavatelj kartice potvrdio je da se podatci o adresi i poštanskom broju slažu s onima u njihovim podatcima';
$_['text_address_yna']			= 'Izdavatelj kartice potvrdio je da se podatci o adresi slažu s onima u njihovim podatcima, ali ne slažu se podatci o poštanskom broju';
$_['text_address_nyz']			= 'Izdavatelj kartice potvrdio je da se podatci o poštanskom broju slažu s onima u njihovim podatcima, ali ne slažu se podatci o adresi';
$_['text_address_nnn']			= 'Oba podatka i adresa i poštanski broj ne slažu se s podatcima kod izdavatelja kartice';
$_['text_address_ypx']			= 'Izdavatelj kartice potvrdio je da se podatci o adresi slažu s onima u njihovim podatcima. Izdavatelj nije provjerio poštanski broj';
$_['text_address_pyx']			= 'Izdavatelj kartice potvrdio je da se podatci o poštanskom broju slažu s onima u njihovim podatcima. Izdavatelj nije provjerio adresu';
$_['text_address_xxu']			= 'Izdavatelj kartice nije provjerio AVS (Address Verification Service) informacije';
$_['text_card_code_verify']		= 'Sigurnosni kod: ';
$_['text_card_code_m']			= 'Sigurnosni kod katice odgovara';
$_['text_card_code_n']			= 'Sigurnosni kod katice ne odgovara';
$_['text_card_code_p']			= 'Nije obrađena/procesirana';
$_['text_card_code_s']			= 'Trgovac je naznačeno da sigurnosni kod sa kartice nije prisutan na kartici';
$_['text_card_code_u']			= 'Izdavatelj nije certificiran i/ili nije osigurao ključeve za šifriranje';
$_['text_card_code_x']			= 'Nema odgovora iz udruge kreditnih kartica da je primljena';
$_['text_card_code_blank']		= 'Prazan odgovor treba značiti da kod nije poslan i da nema nikavih naznaka da kod nije prisutan na kartici.';
$_['text_card_accepted']		= 'Kartice koje prihvaćamo: ';
$_['text_card_type_m']			= 'Mastercard';
$_['text_card_type_v']			= 'Visa (Kreditna/Debitna/Electron/Delta)';
$_['text_card_type_c']			= 'Diners';
$_['text_card_type_a']			= 'American Express';
$_['text_card_type_ma']			= 'Maestro';
$_['text_card_new']				= 'Nova kartica';
$_['text_response_proc_code']	= 'Kod obrade: ';
$_['text_response_ref']			= 'Referentni broj: ';

// Error
$_['error_card_number']			= 'Molim provjerite da li je ispravan broj Vaše kartice';
$_['error_card_name']			= 'Molim provjerite da li je ispravno ime vlasnika/nosioca kartice';
$_['error_card_cvv']			= 'Molim provjerite da li je ispravan sigurnosni kod kartice CVV2';
$_['error_failed']				= 'Ne mogu obraditi/procesirati Vaše plaćanje. Molim kontaktirajte trgovca';