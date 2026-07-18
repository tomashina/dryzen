<?php
// Croatian   v.2.x.x     Datum: 01.10.2014		Author: Gigo (Igor Ilić - igor@iligsoft.hr)
// Heading
$_['heading_title']                      = 'WorldPay online plaćanje';

// Text
// $_['text_payment']				 = 'Plaćanja'; // postojalo u verziji OC 2.2.0.0
$_['text_extension']                     = 'Proširenja (extensions)';
$_['text_success']                       = 'Uspješno: Napravili ste promjene u WorldPay korisničkom računu!';
$_['text_edit']                          = 'Izmjeni WorldPay';
// $_['text_worldpay']					 = '<a href="https://business.worldpay.com/partner/opencart" target="_blank"><img src="view/image/payment/worldpay.png" alt="Worldpay" title="Worldpay" style="border: 1px solid #EEEEEE;" /></a>';
// $_['text_successful']				 = 'On - Uvijek uspješno';
// $_['text_declined']					 = 'On - Uvijek odbijeno';
// $_['text_off']						 = 'Off';
$_['text_worldpay']                      = '<a href="https://online.worldpay.com/signup/ee48b6e6-d3e3-42aa-a80e-cbee3f4f8b09" target="_blank"><img src="view/image/payment/worldpay.png" alt="Worldpay" title="Worldpay" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_test']                          = 'Test';
$_['text_live']                          = 'Produkcija (Live)';
$_['text_authenticate']                  = 'Autentifikacija';
$_['text_release_ok']                    = 'Puštanje (Release) je bilo uspješno';
$_['text_release_ok_order']              = 'Puštanje (Release) je bilo uspješno. Status narudžbe ažuriran je na uspješno - podmireno';
$_['text_refund_ok']                     = 'Popust je bio uspješan';
$_['text_refund_ok_order']               = 'Popust je bio uspješan. Status narudžbe ažuriran je na povrat';
$_['text_void_ok']                       = 'Poništenje je bilo uspješno. Status narudžbe ažuriran je na poništen';

// Entry
// $_['entry_merchant']				= 'ID trgovca (merchant)';
// $_['entry_password']				= 'Lozinka za odgovor kod plaćanja';
// $_['entry_callback']				= 'URL za slanje odgovora';
// $_['entry_test']					= 'Test mod';
$_['entry_service_key']                  = 'Servisni ključ (Service Key)';
$_['entry_client_key']                   = 'Klijentski ključ (Client Key)';
$_['entry_total']                        = 'Ukupno';
$_['entry_order_status']                 = 'Status narudžbe';
$_['entry_geo_zone']                     = 'Geo zona';
$_['entry_status']                       = 'Status';
$_['entry_sort_order']                   = 'Redoslijed sortiranja';
$_['entry_debug']                        = 'Debug mode (zapisivanje u log file)';
$_['entry_card']                         = 'Pohranjivanje kartica';
$_['entry_secret_token']                 = 'Tajni Token';
$_['entry_webhook_url']                  = 'Webhook URL:'; //------------
$_['entry_cron_job_url']                 = 'Cron Job URL:';
$_['entry_last_cron_job_run']            = 'Vrijeme zadnjih pokrenutih cron job-ova:';
$_['entry_success_status']               = 'Status za uspješno:';
$_['entry_failed_status']                = 'Status za neuspješno:';
$_['entry_settled_status']               = 'Status za do podmirenja:';
$_['entry_refunded_status']              = 'Status za vraćen:';
$_['entry_partially_refunded_status']	 = 'Status za djelomično vraćen:';
$_['entry_charged_back_status']			 = 'Status za storno naplate (Charged Back):';
$_['entry_information_requested_status'] = 'Informacije o zatraženom statusu:';
$_['entry_information_supplied_status']  = 'Informacije o isporučenom (Supplied) statusu:';
$_['entry_chargeback_reversed_status']   = 'Status za ukidanja storno naplate (Chargeback Reversed):';
$_['entry_reversed_status']              = 'Reversed Status:'; //------------
$_['entry_voided_status']                = 'Status za poništenje:';

// Help
// $_['help_password']					= 'Ovo mora biti postavljeno u WordPay kontrolnoj ploči.';
// $_['help_callback']					= 'Ovo mora biti postavljeno u WordPay kontrolnoj ploči. Tkođer morate provjeriti da li je uključena opcija "Enable the Shopper Response".';
$_['help_total']                         = 'Minimalna ukupna suma koja mora biti na narudžbi prije nego što ovaj način plaćanja postane aktivan.';
$_['help_debug']                         = 'Omogućavanjem Debug moda osjetljivi podatci bit će zapisani u log datoteku/file. Ovo uvijek trebate isključiti osim ukoliko Vas nisu drugačije uputili.';
$_['help_secret_token']                  = 'Napravite ovo vrlo dugačko i teško za pogoditi';
$_['help_webhook_url']                   = 'Postavite Worldpay webhook-ove da pozivaju ovaj URL'; //------------
$_['help_cron_job_url']                  = 'Postavite cron job da poziva ovaj URL';

// Tab
$_['tab_settings']                       = 'Postavke';
$_['tab_order_status']                   = 'Status narudžbe';

// Error
$_['error_permission']                   = 'Upozorenje: Nemate ovlasti da mijenjate WorldPay korisnički račun!';
// $_['error_merchant']				= 'ID trgovca (merchant) je obavezan podatak!';
// $_['error_password']				= 'Lozinka je obavezan podatak!';
$_['error_service_key']                  = 'Servisni ključ (Service Key) je obavezan podatak!';
$_['error_client_key']                   = 'Klijentski ključ je obavezan podatak!';

// Order page - payment tab
$_['text_payment_info']                  = 'Inforamcije o plaćanju';
$_['text_refund_status']                 = 'Povrat plaćanja';
$_['text_order_ref']                     = 'Referenca narudžbe (po narudžbi)';
$_['text_order_total']                   = 'Ukupno autorizirano';
$_['text_total_released']                = 'Ukupno pušteno';
$_['text_transactions']                  = 'Transakcije';
$_['text_column_amount']                 = 'Iznos';
$_['text_column_type']                   = 'Vrsta/tip';
$_['text_column_date_added']             = 'Dodano';

$_['text_confirm_refund']                = 'Jeste li sigurni da želite napraviti povrat plaćanja?';

$_['button_refund']                      = 'Popust / povrat';