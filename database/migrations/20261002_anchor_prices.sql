-- DryZen / OpenCart 3.0.3.8
-- Sidrena cijena i referentni datum proizvoda.
-- Skripta je idempotentna i može se sigurno pokrenuti više puta.
-- Projekt koristi prefiks tablica `oc_`; prilagodite ga ako je na live bazi drukčiji.

SET @dryzen_schema := DATABASE();
SET @dryzen_old_sql_mode := @@SESSION.sql_mode;
SET @dryzen_sql_mode := CONCAT(',', @@SESSION.sql_mode, ',');
SET @dryzen_sql_mode := REPLACE(@dryzen_sql_mode, ',NO_ZERO_IN_DATE,', ',');
SET @dryzen_sql_mode := REPLACE(@dryzen_sql_mode, ',NO_ZERO_DATE,', ',');
SET SESSION sql_mode = TRIM(BOTH ',' FROM @dryzen_sql_mode);

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_product'
      AND `COLUMN_NAME` = 'anchor_price'
  ),
  'DO 0',
  'ALTER TABLE `oc_product` ADD COLUMN `anchor_price` decimal(15,4) NOT NULL DEFAULT 0.0000 AFTER `price`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_product'
      AND `COLUMN_NAME` = 'anchor_price_date'
  ),
  'DO 0',
  'ALTER TABLE `oc_product` ADD COLUMN `anchor_price_date` date NULL DEFAULT NULL AFTER `anchor_price`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

-- Sidrena cijena za 10.09.2026. je tadašnja redovna cijena, bez akcijskih i
-- količinskih popusta. Puni samo proizvode kojima oba polja još nisu unesena.
UPDATE `oc_product` AS `p`
SET
  `p`.`anchor_price` = `p`.`price`,
  `p`.`anchor_price_date` = '2026-09-10'
WHERE `p`.`anchor_price` = 0.0000
  AND (`p`.`anchor_price_date` IS NULL OR `p`.`anchor_price_date` = '0000-00-00');

SET @dryzen_sql := NULL;
SET @dryzen_schema := NULL;
SET SESSION sql_mode = @dryzen_old_sql_mode;
SET @dryzen_old_sql_mode := NULL;
SET @dryzen_sql_mode := NULL;
