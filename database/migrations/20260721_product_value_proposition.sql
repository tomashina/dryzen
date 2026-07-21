-- DryZen / OpenCart 3
-- Istaknuta poruka o trajnosti/vrijednosti iznad cijene proizvoda.
-- Skripta je idempotentna i može se sigurno pokrenuti više puta.
-- Projekt koristi prefiks tablica `oc_`; prilagodite ga ako je na live bazi drukčiji.

SET @dryzen_schema := DATABASE();

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_product_description'
      AND `COLUMN_NAME` = 'value_proposition'
  ),
  'SELECT ''oc_product_description.value_proposition already exists'' AS message',
  'ALTER TABLE `oc_product_description` ADD COLUMN `value_proposition` varchar(500) NOT NULL DEFAULT '''' AFTER `name`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_product` AS `p`
  ON `p`.`product_id` = `pd`.`product_id`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'Malo pakiranje. Dugotrajna zaštita. Jedno pakiranje od 10 ml dovoljno je za do 6 mjeseci korištenja.'
WHERE `p`.`model` IN ('009', '010', '011', '012')
  AND `l`.`code` = 'hr-hr';

SELECT ROW_COUNT() AS `roll_on_rows_updated`;

SET @dryzen_sql := NULL;
SET @dryzen_schema := NULL;
