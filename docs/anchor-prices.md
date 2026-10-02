# Sidrene cijene

Implementacija dodaje sidrenu cijenu i referentni datum proizvodima u
OpenCartu 3.0.3.8. Vrijednosti se mogu uređivati na obrascu proizvoda ili
masovno uvesti iz popisa proizvoda.

## Aktivacija

Nakon prijenosa datoteka pokrenite idempotentnu migraciju:

```bash
mysql -u KORISNIK -p NAZIV_BAZE < database/migrations/20261002_anchor_prices.sql
```

Migracija dodaje stupce `anchor_price` i `anchor_price_date`. Proizvodima koji
još nemaju nijednu od tih vrijednosti postavlja redovnu cijenu iz
`product.price`, bez akcijskih i količinskih popusta, te referentni datum
`2026-09-10`. Ponovno pokretanje ne prepisuje naknadne ručne izmjene.

Zatim u administraciji otvorite **Extensions > Modifications** i osvježite
OCMOD cache ili iz korijena projekta pokrenite:

```bash
php scripts/refresh-ocmod.php
```

## CSV uvoz

Na popisu proizvoda gumb za preuzimanje daje CSV predložak, a susjedni gumb
otvara uvoz. Datoteka može koristiti zarez ili točka-zarez. Obvezni su:

- jedan identifikator: `product_id`, `model`, `sku` ili `ean`
- `anchor_price`
- `anchor_price_date` u obliku `YYYY-MM-DD`

Primjer:

```csv
model;anchor_price;anchor_price_date
001;12,50;2026-09-10
002;18.90;2026-09-10
```

Iznos se sprema kao OpenCart osnovna cijena bez poreza, jednako kao polje
`product.price`; storefront ga prikazuje s pripadajućim porezom i valutom.
Cijela datoteka validira se prije upisa pa pogrešan red ne uzrokuje djelomičan
uvoz. Najviše je dopušteno 10.000 podatkovnih redova i datoteka od 2 MB.

Automatizirana provjera CSV parsera pokreće se naredbom:

```bash
php scripts/test-anchor-prices.php
```
