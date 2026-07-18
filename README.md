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

## Git remote

Nakon izrade praznog udaljenog repozitorija:

```bash
git remote add origin URL_NOVOG_REPOZITORIJA
git push -u origin main
```

SQL dumpovi, produkcijske vjerodajnice, lokalne konfiguracije, logovi, sessioni,
cache i generirane OCMOD datoteke isključeni su iz repozitorija.
