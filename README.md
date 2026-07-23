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

Nakon postavljanja BOX NOW tracking izmjena pokrenite idempotentnu migraciju:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20260721_boxnow_tracking_email.sql
```

Zatim u administraciji otvorite **Extensions > Modifications** i osvježite
OCMOD cache. Migracija se može sigurno pokrenuti više puta.

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
GA4 modul i ova skripta ga ne prepisuje.

## Git remote

Nakon izrade praznog udaljenog repozitorija:

```bash
git remote add origin URL_NOVOG_REPOZITORIJA
git push -u origin main
git push -u origin codex/otp-compliance
```

SQL dumpovi, produkcijske vjerodajnice, lokalne konfiguracije, logovi, sessioni,
cache i generirane OCMOD datoteke isključeni su iz repozitorija.
