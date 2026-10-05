# Deploy: dostava, sidrene cijene i potrošačka usklađenost

Ove upute vrijede za OpenCart 3.0.3.8 i paket izmjena na grani
`codex/otp-compliance`.

## 1. Sigurnosna kopija i prijenos koda

1. Napravite sigurnosnu kopiju produkcijske baze i direktorija `upload`.
2. Dohvatite i postavite zadnju verziju grane:

   ```bash
   git fetch origin
   git checkout codex/otp-compliance
   git pull --ff-only origin codex/otp-compliance
   ```

3. Ne prepisujte produkcijske datoteke `upload/config.php`,
   `upload/admin/config.php` ni `upload/env.php` lokalnim primjerima.
4. Provjerite da je prenesena službena slika
   `upload/image/catalog/legal/eu-legal-guarantee-hr.png`.

## 2. Migracije baze

Migracije pokrenite ovim redom. Sve su idempotentne i mogu se sigurno ponoviti:

```bash
mysql -u KORISNIK -p BAZA < database/migrations/20261002_anchor_prices.sql
mysql -u KORISNIK -p BAZA < database/migrations/20261002_return_requests.sql
mysql -u KORISNIK -p BAZA < database/migrations/20261002_eurosender_shipping.sql
```

Prva migracija popunjava samo prazne sidrene podatke. Za postojeće proizvode
upisuje redovnu OpenCart cijenu i referentni datum `2026-09-10`, bez prepisivanja
ručno postavljenih vrijednosti.

## 3. OCMOD i cache

Nakon migracija u administraciji otvorite **Extensions > Modifications** i
kliknite **Refresh**. Ako se deploy radi iz projekta s ispravnom lokalnom
OpenCart konfiguracijom, isto se može napraviti naredbom:

```bash
php scripts/refresh-ocmod.php
```

Zatim očistite theme/cache kroz OpenCart dashboard ako je uključen produkcijski
cache ili CDN.

## 4. Digitalni XML/CSV cjenik

1. Otvorite **Extensions > Extensions > Feeds**.
2. Instalirajte **Digitalni XML/CSV cjenik** i otvorite postavke.
3. Provjerite oblik objekta `webshop`, adresu, oznaku `WEB-01`, jedinicu `kom`,
   minimalno 30 dana arhive i status **Enabled**.
4. Spremite te kliknite **Generiraj sada**.
5. Na javnoj stranici **Cjenici** provjerite aktualni XML, CSV i javnu arhivu.
6. Zaštićeni cron URL iz admina pokrećite radnim danom prije 08:00, primjerice:

   ```cron
   30 7 * * 1-5 curl --fail --silent --show-error 'CRON_URL_IZ_ADMINA' >/dev/null
   ```

Direktorij `DIR_STORAGE/digital_pricelist` mora biti zapisiv PHP procesu. Tajni
cron ključ ne upisujte u Git, e-mail ili javnu dokumentaciju.

## 5. Eurosender

1. U `upload/env.php` postavite ključ za odgovarajuće okruženje:

   ```php
   'eurosender' => [
       'sandbox_api_key' => 'SANDBOX_API_KLJUC',
       'production_api_key' => 'PRODUKCIJSKI_API_KLJUC',
   ],
   ```

   Modul automatski odabire odgovarajući ključ prema postavci Sandbox ili
   Production. Postojeće instalacije s jednim poljem `api_key` ostaju podržane,
   ali prije produkcije preporučuje se razdvojiti ključeve kao iznad.

2. Otvorite **Extensions > Extensions > Shipping**, instalirajte i uključite
   **Eurosender**.
3. Odaberite Sandbox za probu ili Production za stvarne rezervacije te potvrdite
   polazišnu adresu, dimenzije i mase paketa.
4. Napravite probnu narudžbu i provjerite ponude dostave. U adminu se naplativa
   rezervacija kreira tek nakon pregleda cijene i izričite potvrde.

Bez valjanog API ključa stvarni Eurosender izračun i rezervacija ne rade. Ako je
konfigurirana procijenjena pričuvna cijena, checkout može prikazati samo tu
procjenu.

## 6. Povrati, jamstvo i završna provjera

Provjerite sljedeće:

- na kategoriji i proizvodu piše sidrena cijena s datumom `10/09/2026`;
- u footeru postoje **Jednostrani raskid ugovora**, **Cjenici** i
  **Zakonsko jamstvo – najmanje 2 godine**;
- bijela traka jamstva je neposredno između newslettera i footera, bez razmaka;
- službena obavijest otvara se na desktopu i mobitelu te se može otvoriti u punoj
  veličini;
- checkout odvojeno prikazuje jamstvo i pravo na raskid u 14 dana;
- probna potvrda narudžbe sadrži tekst, sliku i PNG privitak obavijesti;
- javni obrazac za povrat/raskid šalje potvrdu kupcu i administratoru;
- admin može otvoriti zahtjev i izvesti označene zahtjeve u CSV;
- aktualni i arhivski XML/CSV URL-ovi vraćaju HTTP 200.

Lokalne automatizirane provjere:

```bash
php scripts/test-anchor-prices.php
php scripts/test-digital-pricelist.php
php scripts/test-return-requests.php
php scripts/test-legal-guarantee.php
php scripts/test-eurosender-shipping.php
php scripts/test-boxnow-automation.php
php scripts/test-order-mail.php
```
