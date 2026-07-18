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

## OTP sadržaj i zahtjevi kartičnog plaćanja

Nakon uvoza postojeće baze pokrenite migraciju sadržaja:

```bash
php scripts/apply-otp-compliance.php
```

Skripta ažurira podatke o trgovcu, Opće uvjete kupnje, načine plaćanja i
dostavu, sigurnost plaćanja te povrate i reklamacije. Može se sigurno pokrenuti
više puta. Nakon njezina pokretanja ponovno osvježite OCMOD cache u
administraciji.

## Git remote

Nakon izrade praznog udaljenog repozitorija:

```bash
git remote add origin URL_NOVOG_REPOZITORIJA
git push -u origin main
git push -u origin codex/otp-compliance
```

SQL dumpovi, produkcijske vjerodajnice, lokalne konfiguracije, logovi, sessioni,
cache i generirane OCMOD datoteke isključeni su iz repozitorija.
