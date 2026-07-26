# Google Analytics i Google Tag Manager

## Otvaranje modula

1. Prijavite se u OpenCart administraciju.
2. U lijevom izborniku otvorite **Complete Google Analytics + GA4**, kao na
   priloženoj snimci zaslona. Nakon otvaranja naslov stranice glasi
   **Google Analytics + GA4 + Google Tag Manager**.
3. Ako stavka nije vidljiva, otvorite **Extensions > Modifications**, kliknite
   plavi gumb **Refresh**, pa ponovno učitajte administraciju.
4. Ako je i dalje nema, u **System > Users > User Groups** svojoj grupi
   omogućite `access` i `modify` dozvole za `extension/cmpltguagaf`.

Modul se može otvoriti i izravnom rutom:

```text
/admin/index.php?route=extension/cmpltguagaf&user_token=VAŠ_TRENUTNI_TOKEN
```

Token nije stalan. Najsigurnije je otvoriti modul iz lijevog izbornika.

## Dodavanje Google Tag Managera

1. U [Google Tag Manageru](https://tagmanager.google.com/) otvorite web-spremnik
   i kopirajte njegov ID, primjerice `GTM-ABCDEFG`.
2. U modulu odaberite karticu odgovarajuće trgovine.
3. Postavite **Status** na **Aktivirano**.
4. U polje **Google Tag Manager ID** upišite samo ID spremnika, bez cijelog
   JavaScript isječka.
5. Kliknite **Zapamti**.
6. U Google Tag Manageru objavite verziju spremnika.

Postojeća baza nadograđuje se automatski pri prvom otvaranju modula. Ručno
izvođenje SQL-a nije potrebno.

## GA4: izravno ili kroz GTM

Koristite jednu od ove dvije postavke:

- Izravni GA4: ostavite postojeći `G-...` u polju **GA4 ID**. GTM može ostati
  prazan.
- GA4 kroz GTM: dodajte Google tag u GTM spremnik, upišite `GTM-...` u modul i
  ostavite polje **GA4 ID** prazno.

Nemojte isti `G-...` ID istodobno postaviti u modulu i u GTM spremniku jer se
prikazi stranica i ecommerce događaji mogu zabilježiti dvaput.

## Consent i provjera

GTM spremnik se ne preuzima prije nego što posjetitelj prihvati kategoriju
**Analitika** u DryZen postavkama kolačića. Zadano stanje za Analytics i Ads
pohranu ostaje `denied`, a izbor posjetitelja ažurira Google Consent Mode.

Za provjeru:

1. Otvorite trgovinu u privatnom prozoru.
2. Prihvatite **Analitiku**.
3. U preglednikovom Network panelu potražite
   `googletagmanager.com/gtm.js?id=GTM-...`.
4. Dodatno provjerite spremnik opcijom **Preview** u Google Tag Manageru.

Za novi test izbrišite kolačić `cc_cookie` ili ponovno otvorite privatni prozor.
