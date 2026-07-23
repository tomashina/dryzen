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
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'Jedno pakiranje = jedan kompletan početni tretman (3 uzastopne večeri), a ne samo tri pojedinačne upotrebe.'
WHERE `pd`.`product_id` IN (6, 9, 10, 11, 12, 13, 14, 15)
  AND `l`.`code` = 'hr-hr';

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'Malo pakiranje. Dugotrajna zaštita. Jedno pakiranje od 10 ml dovoljno je za do 6 mjeseci korištenja.'
WHERE `pd`.`product_id` IN (16, 17, 18, 19)
  AND `l`.`code` = 'hr-hr';

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'Jedna bočica. Mjeseci bez kompromisa. Koliko puta ste već kupili jeftiniji sprej koji je završio u smeću nakon nekoliko tjedana?'
WHERE `pd`.`product_id` = 20
  AND `l`.`code` = 'hr-hr';

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'One pack = one complete initial treatment (3 consecutive evenings), not just three individual uses.'
WHERE `pd`.`product_id` IN (6, 9, 10, 11, 12, 13, 14, 15)
  AND `l`.`code` = 'en-gb';

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'Small pack. Long-lasting protection. One 10 ml pack is enough for up to 6 months of use.'
WHERE `pd`.`product_id` IN (16, 17, 18, 19)
  AND `l`.`code` = 'en-gb';

UPDATE `oc_product_description` AS `pd`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `pd`.`language_id`
SET `pd`.`value_proposition` = 'One bottle. Months without compromise. How many times have you bought a cheaper spray that ended up in the bin after just a few weeks?'
WHERE `pd`.`product_id` = 20
  AND `l`.`code` = 'en-gb';

SET @dryzen_sql := NULL;
SET @dryzen_schema := NULL;
