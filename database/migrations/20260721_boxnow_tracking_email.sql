-- DryZen / OpenCart 3
-- BOX NOW tracking broj, status i evidencija tracking emaila.
-- Skripta je idempotentna i može se pokrenuti više puta.
-- Projekt koristi prefiks tablica `oc_`; prilagodite ga ako je na odredišnoj bazi drukčiji.

CREATE TABLE IF NOT EXISTS `oc_boxnow_shipment` (
  `boxnow_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_number` varchar(64) NOT NULL,
  `reference_number` varchar(128) NOT NULL DEFAULT '',
  `parcel_id` varchar(64) NOT NULL DEFAULT '',
  `locker_id` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(64) NOT NULL DEFAULT '',
  `email_sent_at` datetime NULL DEFAULT NULL,
  `email_error` text NULL,
  `payload` mediumtext,
  `response` mediumtext,
  `webhook_payload` mediumtext,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`boxnow_shipment_id`),
  KEY `order_id` (`order_id`),
  KEY `parcel_id` (`parcel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET @dryzen_schema := DATABASE();

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'email_sent_at'
  ),
  'SELECT ''oc_boxnow_shipment.email_sent_at already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `email_sent_at` datetime NULL DEFAULT NULL AFTER `status`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_boxnow_shipment'
      AND `COLUMN_NAME` = 'email_error'
  ),
  'SELECT ''oc_boxnow_shipment.email_error already exists'' AS message',
  'ALTER TABLE `oc_boxnow_shipment` ADD COLUMN `email_error` text NULL AFTER `email_sent_at`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_schema
      AND `TABLE_NAME` = 'oc_order'
      AND `COLUMN_NAME` = 'boxnow'
  ),
  'SELECT ''oc_order.boxnow already exists'' AS message',
  'ALTER TABLE `oc_order` ADD COLUMN `boxnow` text NULL AFTER `shipping_code`'
);
PREPARE dryzen_stmt FROM @dryzen_sql;
EXECUTE dryzen_stmt;
DEALLOCATE PREPARE dryzen_stmt;

SET @dryzen_sql := NULL;
SET @dryzen_schema := NULL;
