<?php
$_['heading_title'] = 'e-Računi sinkronizacija';

$_['text_extension'] = 'Proširenja';
$_['text_success'] = 'Postavke e-Računi sinkronizacije su spremljene.';
$_['text_edit'] = 'Cijene i zaliha iz e-Računi';
$_['text_enabled'] = 'Uključeno';
$_['text_disabled'] = 'Isključeno';
$_['text_connection'] = 'API veza';
$_['text_connection_ready'] = 'Vjerodajnice su pronađene u upload/env.php.';
$_['text_connection_missing'] = 'Nedostaju API postavke:';
$_['text_sync'] = 'Ručna sinkronizacija';
$_['text_sync_help'] = 'Proizvodi se povezuju po odabranom polju. Nepostojeće šifre i prazni API odgovori ne mijenjaju proizvode.';
$_['text_prices'] = 'Cijene';
$_['text_stock'] = 'Zaliha';
$_['text_cron'] = 'EasyCron za količine';
$_['text_cron_help'] = 'U EasyCron dodajte ovaj URL kao HTTP GET. Preporučeni interval je svakih 15 minuta. URL sadrži tajni ključ; nemojte ga javno objavljivati.';
$_['text_last_run'] = 'Zadnje izvršavanje';
$_['text_never'] = 'Još nije izvršeno';
$_['text_available'] = 'Raspoloživa zaliha (stanje minus rezervirano)';
$_['text_physical'] = 'Fizičko stanje zalihe';
$_['text_price_net'] = 'Ne, vrijednost je neto';
$_['text_price_gross'] = 'Da, ukloni API PDV prije spremanja';
$_['text_copy'] = 'Kopiraj URL';
$_['text_copied'] = 'URL je kopiran.';

$_['entry_status'] = 'Status modula';
$_['entry_stock_status'] = 'Dopusti sinkronizaciju zalihe';
$_['entry_price_status'] = 'Dopusti sinkronizaciju cijena';
$_['entry_code_field'] = 'Veza šifre artikla';
$_['entry_price_field'] = 'Polje cijene iz e-Računi';
$_['entry_price_includes_tax'] = 'Odabrana cijena uključuje PDV';
$_['entry_stock_mode'] = 'Vrsta količine';
$_['entry_warehouse_code'] = 'Šifra skladišta';
$_['entry_timeout'] = 'API timeout (sekunde)';
$_['entry_cron_key'] = 'Tajni cron ključ';
$_['entry_cron_url'] = 'EasyCron URL';

$_['help_code_field'] = 'Za DryZen koristi Model: OpenCart modeli 001–013 odgovaraju šiframa artikala u e-Računi.';
$_['help_price_field'] = 'Postojeći DryZen konektor koristi grossPrice. retailPrice i purchasePrice dostupni su za drugačije postavljen katalog. Nulte cijene se radi sigurnosti uvijek preskaču.';
$_['help_price_includes_tax'] = 'OpenCart cijenu sprema bez PDV-a kada proizvod ima poreznu klasu. Uključite samo ako odabrano API polje zaista sadrži PDV.';
$_['help_warehouse_code'] = 'Ostavite prazno za zbroj svih skladišta. Upišite šifru samo ako web trgovina koristi jedno određeno skladište.';
$_['help_cron_key'] = 'Promjena ključa odmah poništava prethodni EasyCron URL.';

$_['button_test'] = 'Testiraj vezu';
$_['button_sync_stock'] = 'Ažuriraj količine';
$_['button_sync_prices'] = 'Ažuriraj cijene';

$_['error_permission'] = 'Nemate ovlast za izmjenu e-Računi sinkronizacije.';
$_['error_timeout'] = 'Timeout mora biti između 5 i 120 sekundi.';
$_['error_cron_key'] = 'Cron ključ mora imati najmanje 24 znaka i smije sadržavati slova, brojeve, _ i -.';
