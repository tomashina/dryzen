# Jednostrani raskid i povrat artikala

Modul nadograđuje ugrađeni OpenCart 3.0.3.8 sustav povrata. Javni obrazac nalazi se na ruti
`index.php?route=account/return/add`, dostupan je i gostima, a poveznica je prikazana u
podnožju webshopa. Prijavljeni kupac obrazac može otvoriti i iz detalja narudžbe, kada se
podaci o računu i artiklu automatski popunjavaju.

Obrazac razlikuje nedvosmislenu izjavu o jednostranom raskidu od povrata, zamjene ili
reklamacije artikala. Sprema broj i datum računa, kontakt, strukturirani popis artikala,
razlog, napomenu, opcionalni IBAN i vrijeme prihvaćanja izjave. Zaštićen je jednokratnim
tokenom vezanim uz korisničku sesiju, poslužiteljskom validacijom i postojećim OpenCart
captcha pravilima za stranicu povrata.

Nakon uspješnog slanja, kupac i administrator dobivaju tekstualnu e-mail potvrdu. U e-mailu
je IBAN maskiran; puni podatak dostupan je samo ovlaštenom administratoru. Statusi i povijest
obrađuju se na standardnoj stranici **Prodaja > Povrati**. Odabrani zahtjevi mogu se izvesti
u UTF-8 CSV, po jedan red za svaki vraćeni artikl. Vrijednosti koje bi tablični program mogao
protumačiti kao formule neutraliziraju se pri izvozu.

## Instalacija

1. Prije objave napraviti sigurnosnu kopiju baze.
2. Pokrenuti `database/migrations/20261002_return_requests.sql` nad OpenCart bazom.
3. Objaviti datoteke iz direktorija `upload/`.
4. U administraciji otvoriti **Proširenja > Modifikacije** i kliknuti **Osvježi**. Migracija
   isključuje staru modifikaciju `Basel Custom Return Form OC3`, jer je njezina funkcionalnost
   sada održavana izravno u izvornom kodu.
5. U administraciji provjeriti da korisnička grupa ima `access` i `modify` pravo za
   `sale/return` (standardne OpenCart ovlasti za povrate).
6. U **Postavke > Trgovine > Opcije** postaviti početni status povrata i, po želji,
   informacijsku stranicu uvjeta povrata te captcha zaštitu za povrate.

Migracija je idempotentna i može se ponovno pokrenuti. Lokalna provjera helpera, maskiranja
IBAN-a, zaštite CSV-a, CSRF integracije i mail/export veza pokreće se naredbom:

```sh
php scripts/test-return-requests.php
```
