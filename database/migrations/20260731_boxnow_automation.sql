-- DryZen / OpenCart 3
-- Isključivo dorade za automatsku BOX NOW pošiljku i slanje PDF adresnice.
-- Preduvjet: postojeća tablica `oc_boxnow_shipment` iz BOX NOW integracije.
-- Migracija je idempotentna i može se sigurno pokrenuti više puta.

SET @dryzen_schema := DATABASE();

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1 FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'creation_attempted_at'
  ),
  'SELECT ''oc_boxnow_shipment.creation_attempted_at already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `creation_attempted_at` datetime NULL DEFAULT NULL AFTER `status`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1 FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'creation_error'
  ),
  'SELECT ''oc_boxnow_shipment.creation_error already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `creation_error` text NULL AFTER `creation_attempted_at`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1 FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'label_email_sent_at'
  ),
  'SELECT ''oc_boxnow_shipment.label_email_sent_at already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `label_email_sent_at` datetime NULL DEFAULT NULL AFTER `email_error`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1 FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'label_email_error'
  ),
  'SELECT ''oc_boxnow_shipment.label_email_error already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `label_email_error` text NULL AFTER `label_email_sent_at`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1 FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'label_email_recipients'
  ),
  'SELECT ''oc_boxnow_shipment.label_email_recipients already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `label_email_recipients` text NULL AFTER `label_email_error`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := NULL;
SET @dryzen_schema := NULL;

DELETE FROM `oc_event` WHERE `code` = 'boxnow_auto_shipment';

INSERT INTO `oc_event` (`code`, `trigger`, `action`, `status`, `sort_order`)
VALUES (
  'boxnow_auto_shipment',
  'catalog/model/checkout/order/addOrderHistory/after',
  'event/boxnow/afterOrderHistory',
  1,
  20
);
