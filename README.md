# DryZen internet trgovina

OpenCart 3.0.3.8 projekt za DryZen internet trgovinu.

## Lokalno pokretanje

Projekt je lokalno postavljen za Laravel Herd:

- URL: `http://dryzen.test`
- document root: `/Users/tomek/Herd/dryzen/upload`
- PHP: 7.4
- baza: `dryzegit`
- OpenCart storage: `/Users/tomek/Herd/dryzen/storagedijana`

Lokalne konfiguracije `upload/config.php`, `upload/admin/config.php` i
`upload/env.php` namjerno nisu u Gitu. Pri postavljanju na drugom računalu
kopirajte odgovarajuće `.example.php` datoteke, prilagodite URL, pristup bazi i
eventualne integracije te uklonite nastavak `.example` iz imena.

Nakon uvoza baze u administraciji otvorite **Extensions > Modifications** i
kliknite gumb za osvježavanje kako bi se ponovno generirao OCMOD cache.

## e-Računi: cijene i zaliha

OpenCart modul **Extensions > Extensions > Modules > e-Računi sinkronizacija**
povezuje proizvode po modelu/SKU-u/EAN-u (za DryZen je zadano polje `model`,
odnosno šifre `001`–`013`). Modul omogućuje ručno ažuriranje cijena i zalihe,
test API veze te generira zaštićeni HTTP GET URL za EasyCron. Cron ažurira samo
količine; preporučeni interval je svakih 15 minuta.

API vjerodajnice ostaju isključivo u ignoriranoj datoteci `upload/env.php` pod
`OC_ENV['import']['api']`. Token, API korisnik i tajni ključ dostupni su u
e-Računi kroz **Postavke > Postavke tvrtke > API Web services**. Polje
`password` sadrži Secret key API korisnika, a ne lozinku za običnu prijavu.
Nakon promjene vjerodajnica prvo upotrijebite gumb **Testiraj vezu**, a zatim
ručnu sinkronizaciju.

Sinkronizacija količine koristi `WarehouseGetArticleStockQuantity`, a cijene
`ProductList`. Prazan ili neprepoznatljiv API odgovor nikada ne postavlja sve
proizvode na nulu. Istovremena cron izvršavanja zaštićena su MySQL lockom.

## BOX NOW tracking

BOX NOW pošiljka automatski se kreira kada narudžba prvi put uđe u jedan od
OpenCart processing/complete statusa. Nakon uspješnog API odgovora kupcu se
šalje tracking kod, a trgovini i dodatnim adresama označenima za obavijesti o
narudžbama šalje se zaseban email s PDF adresnicom. Admin narudžbe zadržava
ručne gumbe za ponovni pokušaj.

Nakon postavljanja BOX NOW izmjena pokrenite idempotentnu migraciju:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20260731_boxnow_automation.sql
```

Migracija dodaje evidenciju pokušaja i slanja adresnice te registrira OpenCart
event `boxnow_auto_shipment`. Zatim u administraciji otvorite **Extensions >
Modifications** i osvježite OCMOD cache. Migracija se može sigurno pokrenuti
više puta.

Automatizirane provjere BOX NOW toka pokreću se naredbom:

```bash
php scripts/test-boxnow-automation.php
```

Test koristi lažne API/PDF/mail odgovore i ne kreira stvarne BOX NOW pošiljke.

## Eurosender dostava

OpenCart modul **Extensions > Extensions > Shipping > Eurosender** dohvaća
aktualne Eurosender cijene za Standard, Priority i Express usluge. Modul je
pripremljen za Eurosender Sandbox (`https://sandbox-api.eurosender.com`) i
produkciju (`https://api.eurosender.com`). Sandbox i produkcijski API ključevi
nisu zamjenjivi.

API ključ ostaje isključivo u ignoriranoj datoteci `upload/env.php`:

```php
'eurosender' => [
    'api_key' => 'SANDBOX_ILI_PRODUCTION_KLJUC',
],
```

Dok proizvodi nemaju upisane stvarne mase i dimenzije, zadana procjena paketa
je 20 × 15 × 10 cm, 0,20 kg ambalaže i 0,10 kg po artiklu, uz minimalnu
obračunsku masu od 0,50 kg. Sve se vrijednosti mogu promijeniti u postavkama
modula. Prije produkcije treba ih potvrditi vaganjem zapakiranih narudžbi.

Kreiranje Eurosender pošiljke namjerno je ručna radnja na detalju narudžbe:
API poziv stvara obvezu plaćanja. Admin najprije dobiva svježu cijenu, uslugu,
iznos dostave naplaćen kupcu i razliku te ih mora izričito potvrditi. Neposredno
prije naplativog zahtjeva modul još jednom dohvaća cijenu i validira podatke; ako
cijena poraste makar 0,01 EUR, rezervacija se zaustavlja i traži novu potvrdu.
Jednokratna potvrda vezana je uz narudžbu, admina i API okruženje. Modul koristi
bazni lock protiv paralelnog dvostrukog kreiranja i nakon neodređenog timeouta
ne pokušava automatski ponovno. Labela i tracking osvježavaju se kroz admin.

Nakon postavljanja izmjena pokrenite idempotentnu migraciju:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20261002_eurosender_shipping.sql
```

Zatim instalirajte/uključite Eurosender u OpenCart administraciji. Sigurnosne
detalje webhook potpisa Eurosender javno ne dokumentira, pa modul zasad koristi
kontrolirano osvježavanje statusa umjesto nepotvrđenog webhook handlera.
Nakon postavljanja datoteka osvježite OCMOD cache u administraciji ili pokrenite
`php scripts/refresh-ocmod.php` kako bi se novi panel prikazao na narudžbi.

## Digitalni XML/CSV cjenik

OpenCart feed **Extensions > Extensions > Feeds > Digitalni XML/CSV cjenik**
objavljuje aktivne proizvode s kodom, markom, jedinicom i jediničnom cijenom,
aktualnom bruto i sidrenom cijenom, podacima o akciji/popustu, barkodom i
dostupnošću. XML i CSV dostupni su na
javnim URL-ovima prikazanima u postavkama feeda. Datoteke se generiraju u
privatnom `DIR_STORAGE/digital_pricelist` direktoriju, izvan web-roota, a javni
kontroler poslužuje samo verziju navedenu u atomarno zapisanom manifestu.

Nakon deploya najprije primijenite migraciju sidrenih cijena, zatim u adminu
otvorite **Extensions > Extensions**, odaberite **Feeds**, instalirajte
**Digitalni XML/CSV cjenik**, spremite postavke i kliknite **Generiraj sada**.
Produkcijski javni URL-ovi tada su:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20261002_anchor_prices.sql
```

```text
https://www.dryzen.eu/index.php?route=extension/feed/digital_pricelist&format=xml
https://www.dryzen.eu/index.php?route=extension/feed/digital_pricelist&format=csv
```

Pri instalaciji feed automatski izrađuje snažan tajni cron ključ. Za svakodnevno
ažuriranje postavite prikazani zaštićeni cron URL na jedan HTTP GET dnevno
(radnim danom primjerice u 07:30, prije 08:00). Prvi javni zahtjev u novom danu također pokreće osvježavanje
ako cron nije izvršen. Svako uspješno generiranje ostaje kao nepromjenjivi XML i
CSV snapshot; stare verzije čuvaju se i javno su dostupne najmanje 30 dana.
Naziv datoteke sadrži oblik i adresu objekta, njegovu oznaku, redni broj pohrane
i vrijeme objave. Neuspjelo osvježavanje ne
uklanja prethodnu važeću verziju.

Primjer sistemskog crona (ključ se kopira iz admina i ne objavljuje):

```cron
30 7 * * 1-5 curl --fail --silent --show-error 'https://www.dryzen.eu/index.php?route=extension/feed/digital_pricelist/cron&key=TAJNI_KLJUC_IZ_ADMINA' >/dev/null
```

Automatizirana provjera generatora pokreće se naredbom:

```bash
php scripts/test-digital-pricelist.php
```

Objedinjeni produkcijski postupak za ovaj paket nalazi se u
[`docs/deployment-20261002.md`](docs/deployment-20261002.md).

## Istaknuta vrijednost proizvoda

Za poruku o trajnosti/vrijednosti iznad cijene proizvoda pokrenite:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20260721_product_value_proposition.sql
```

Migracija dodaje višejezično admin polje te početni hrvatski tekst postavlja na
roll-on modele `009`–`012`. Nakon prijenosa izmijenjenih datoteka osvježite
**Extensions > Modifications** u OpenCart administraciji.

## OTP sadržaj i zahtjevi kartičnog plaćanja

Nakon uvoza postojeće baze pokrenite migraciju sadržaja:

```bash
php scripts/apply-otp-compliance.php
```

Skripta ažurira podatke o trgovcu, Opće uvjete kupnje, načine plaćanja i
dostavu, sigurnost plaćanja te povrate i reklamacije. Može se sigurno pokrenuti
više puta. Nakon njezina pokretanja ponovno osvježite OCMOD cache u
administraciji.

## Cookie consent i tracking

DryZen koristi vlastiti CookieConsent 3 modul koji odvojeno upravlja nužnim,
analitičkim i marketinškim kolačićima. Google Analytics i Meta Pixel ostaju
neaktivni dok posjetitelj ne prihvati pripadajuću kategoriju.

Nakon uvoza baze ili postavljanja tracking izmjena pokrenite:

```bash
php scripts/apply-tracking-consent.php --refresh
```

Skripta postavlja Meta Pixel `3630339670455864`, uključuje događaje `PageView`,
`AddToCart` i `Purchase`, isključuje stari GDPR banner te prilagođava postojeći
Meta OCMOD za odgođeno učitavanje. Bez opcije `--refresh` potrebno je ručno
osvježiti **Extensions > Modifications** u OpenCart administraciji. Meta ad
account `2473286479801468` konfigurira se u Meta Business sučelju i nije dio
browser pixel snippeta. Produkcijski Google Analytics Measurement ID je
`G-X8YR3077DZ`; ostaje konfiguriran kroz postojeći Complete Google Analytics +
GA4 modul i ova skripta ga ne prepisuje. Modul podržava i Google Tag Manager
web-spremnik u formatu `GTM-XXXXXXX`. Otvara se u administraciji preko stavke
**Complete Google Analytics + GA4** (odnosno novog naslova
**Google Analytics + GA4 + Google Tag Manager**) u lijevom izborniku. GTM se
učitava tek nakon prihvaćanja analitičkih kolačića.

Ako je isti GA4 mjerni ID postavljen unutar GTM spremnika, polje **GA4 ID** u
modulu treba ostaviti prazno kako bi se izbjeglo dvostruko bilježenje događaja.
Detaljne upute nalaze se u
[`docs/google-analytics-tag-manager.md`](docs/google-analytics-tag-manager.md).

## Git remote

Nakon izrade praznog udaljenog repozitorija:

```bash
git remote add origin URL_NOVOG_REPOZITORIJA
git push -u origin main
git push -u origin codex/otp-compliance
```

SQL dumpovi, produkcijske vjerodajnice, lokalne konfiguracije, logovi, sessioni,
cache i generirane OCMOD datoteke isključeni su iz repozitorija.
