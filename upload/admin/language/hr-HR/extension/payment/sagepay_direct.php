<?php
// Croatian   v.2.x.x     Datum: 01.10.2014		Author: Gigo (Igor Ilić - igor@iligsoft.hr)
// Heading
$_['heading_title']           = 'SagePay Direct';

// Text
// $_['text_payment']					= 'Plaćanja';  // postojalo u verziji OC 2.2.0.0
$_['text_extension']          = 'Proširenja (extensions)';
$_['text_success']            = 'Uspješno: Napravili ste promjene u SagePay korisničkom računu!';
$_['text_edit']               = 'Izmjeni SagePay Direct';
$_['text_sagepay_direct']     = '<a href="https://support.sagepay.com/apply/default.aspx?PartnerID=E511AF91-E4A0-42DE-80B0-09C981A3FB61" target="_blank"><img src="view/image/payment/sagepay.png" alt="SagePay" title="SagePay" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_sim']                = 'Simulator';
$_['text_test']               = 'Test';
$_['text_live']               = 'Produkcija (live)';
$_['text_defered']            = 'Odgođen';
$_['text_authenticate']       = 'Potvrđen';
$_['text_release_ok']         = 'Release was successful'; //------------
$_['text_release_ok_order']   = 'Release was successful, order status updated to success - settled'; //------------
$_['text_rebate_ok']          = 'Popust je uspješno dodan';
$_['text_rebate_ok_order']    = 'Popust je uspješno dodan, status narudžbe ažuriran je na plaćanje s popustom';
$_['text_void_ok']            = 'Poništenje je uspješno, status narudžbe ažuriran je na poništena';
$_['text_payment_info']       = 'Informacije o plaćanju';
$_['text_release_status']     = 'Payment released'; //------------
$_['text_void_status']        = 'Plaćanje poništeno';
$_['text_rebate_status']      = 'Plaćanje s popustom';
$_['text_order_ref']          = 'Order ref'; //------------
$_['text_order_total']        = 'Ukupno autorizirano';
$_['text_total_released']     = 'Total released'; //------------
$_['text_transactions']       = 'Transakcije';
$_['text_column_amount']      = 'Iznos';
$_['text_column_type']        = 'Vrsta/tip';
$_['text_column_date_added']  = 'Kreirano (datum dodavanja)';
$_['text_confirm_void']       = 'Jeste li sigurni da želite poništiti plaćanje?';
$_['text_confirm_release']    = 'Are you sure you want to release the payment?'; //------------
$_['text_confirm_rebate']     = 'Jeste li sigurni da želite odobriti rabat na plaćanje?';


// Entry
$_['entry_vendor']            = 'Prodavač';
$_['entry_test']              = 'Test mod';
$_['entry_transaction']       = 'Transakcijska metoda';
$_['entry_total']             = 'Ukupno';
$_['entry_order_status']      = 'Status narudžbe';
$_['entry_geo_zone']          = 'Geo zona';
$_['entry_status']            = 'Status';
$_['entry_sort_order']        = 'Redoslijed sortiranja';
$_['entry_debug']             = 'Debug evidencija/logiranje';
$_['entry_card']              = 'Pohraniti kartice';
$_['entry_cron_job_token']    = 'Tajni znak (token)';
$_['entry_cron_job_url']      = 'URL za Cron Job';
$_['entry_last_cron_job_run'] = 'Zadnje vrijeme kad je cron job pokrenut:';

// Help
$_['help_total']              = 'Minimalna ukupna suma koja mora biti na narudžbi prije nego što ovaj način plaćanja postane aktivan.';
$_['help_debug']              = 'Uključivanje ove opcije omogućuje podatcima da budu dodani u Vašu datoteku s greškama (error log) kako biste lakše pronašli (debug-irali) bilo koje probleme. Ovo uvijek možete isključiti osim ukoliko nije drugačije navedeno u uputama/instrukcijama.';
$_['help_transaction']        = 'Metoda transakcije MORA biti postavljena na Plaćanje da bi se omogućilo plaćanje pretplate';
$_['help_cron_job_token']     = 'Napravite ovo da bude dugačko i teško za pogoditi';
$_['help_cron_job_url']       = 'Postavite cron job da pozica ovaj URL';

// Button
$_['button_release']          = 'Release';
$_['button_rebate']           = 'Popust / povrat';
$_['button_void']             = 'Poništi';

// Error
$_['error_permission']        = 'Upozorenje: Nemate ovlasti da mijenjate SagePay korisnički račun!';
$_['error_vendor']            = 'ID prodavača je obavezan podatak!';