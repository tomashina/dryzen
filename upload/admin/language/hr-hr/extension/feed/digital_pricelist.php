<?php
$_['heading_title'] = 'Digitalni XML/CSV cjenik';

$_['text_extension'] = 'Proširenja';
$_['text_success'] = 'Postavke digitalnog cjenika su spremljene.';
$_['text_edit'] = 'Javni digitalni cjenik';
$_['text_enabled'] = 'Uključeno';
$_['text_disabled'] = 'Isključeno';
$_['text_public_urls'] = 'Javni URL-ovi';
$_['text_schedule'] = 'Dnevno automatsko ažuriranje';
$_['text_schedule_help'] = 'Postavite zaštićeni cron URL radnim danom prije 08:00, primjerice u 07:30. Ako cron izostane, prvi javni zahtjev toga dana sigurno će osvježiti cjenik.';
$_['text_archive_help'] = 'Svako uspješno generiranje stvara nepromjenjivu XML i CSV verziju. Javni popis i preuzimanje zadržanih verzija dostupni su najmanje 30 dana.';
$_['text_object_details'] = 'Podaci prodajnog objekta i datoteke';
$_['text_object_help'] = 'Ovi podaci ulaze u propisani naziv svake datoteke zajedno s rednim brojem pohrane i vremenom objave.';
$_['text_last_generation'] = 'Zadnje generiranje';
$_['text_product_count'] = 'Broj proizvoda';
$_['text_never'] = 'Cjenik još nije generiran';
$_['text_generated'] = 'Digitalni cjenik je uspješno generiran.';
$_['text_copy'] = 'Kopiraj';
$_['text_copied'] = 'URL je kopiran.';

$_['entry_status'] = 'Status';
$_['entry_archive_days'] = 'Čuvanje arhive (dani)';
$_['entry_xml_url'] = 'Javni XML URL';
$_['entry_csv_url'] = 'Javni CSV URL';
$_['entry_cron_key'] = 'Tajni cron ključ';
$_['entry_cron_url'] = 'Zaštićeni dnevni cron URL';
$_['entry_object_type'] = 'Oblik prodajnog objekta';
$_['entry_object_address'] = 'Adresa prodajnog objekta';
$_['entry_object_designation'] = 'Oznaka prodajnog objekta';
$_['entry_unit'] = 'Zadana jedinica mjere';

$_['help_archive_days'] = 'Najmanje 30 dana; preporučeno je zadržati veću vrijednost ako poslovna pravila to traže.';
$_['help_cron_key'] = 'Promjena ključa odmah poništava prethodni cron URL. Javni XML i CSV URL-ovi nemaju tajni ključ.';

$_['button_generate'] = 'Generiraj sada';

$_['error_permission'] = 'Nemate ovlast za izmjenu digitalnog cjenika.';
$_['error_method'] = 'Generiranje digitalnog cjenika dopušteno je samo POST zahtjevom.';
$_['error_archive_days'] = 'Arhiva se mora čuvati između 30 i 3650 dana.';
$_['error_cron_key'] = 'Cron ključ mora imati 24 do 128 znakova i smije sadržavati slova, brojeve, _ i -.';
$_['error_object_address'] = 'Upišite adresu prodajnog objekta (najviše 255 znakova).';
$_['error_object_designation'] = 'Upišite oznaku prodajnog objekta (najviše 64 znaka).';
$_['error_unit'] = 'Upišite zadanu jedinicu mjere (najviše 16 znakova).';
$_['error_generation'] = 'Generiranje nije uspjelo. Prethodna važeća verzija ostala je sačuvana.';
