-- DryZen / OpenCart 3.0.3.8
-- Structured unilateral-withdrawal and product-return requests.
-- The migration extends OpenCart's native return table so the standard admin
-- status/history workflow remains available. It is safe to run more than once.

SET @dryzen_return_schema := DATABASE();

-- Older OpenCart schemas use an all-zero default for this DATE column. Modern
-- MySQL strict mode refuses every subsequent ALTER while that default exists.
SET @dryzen_return_sql_mode := @@SESSION.sql_mode;
SET SESSION sql_mode = '';
UPDATE `oc_return` SET `date_ordered` = NULL WHERE `date_ordered` = '0000-00-00';
ALTER TABLE `oc_return` MODIFY `date_ordered` date NULL DEFAULT NULL;
SET SESSION sql_mode = @dryzen_return_sql_mode;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'invoice_number'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `invoice_number` varchar(64) NOT NULL DEFAULT '''' AFTER `order_id`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'invoice_date'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `invoice_date` date NULL DEFAULT NULL AFTER `invoice_number`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'request_type'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `request_type` varchar(24) NOT NULL DEFAULT ''return'' AFTER `invoice_date`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

ALTER TABLE `oc_return` MODIFY `request_type` varchar(24) NOT NULL DEFAULT 'return';

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'refund_iban'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `refund_iban` varchar(64) NOT NULL DEFAULT '''' AFTER `telephone`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'return_items'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `return_items` text NULL AFTER `quantity`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'declaration_at'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `declaration_at` datetime NULL DEFAULT NULL AFTER `comment`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

-- Rows created by the earlier product-return form predate an explicit
-- declaration timestamp, so retain their original meaning as product returns.
UPDATE `oc_return`
SET `request_type` = 'return'
WHERE `declaration_at` IS NULL AND `request_type` = 'withdrawal';

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND COLUMN_NAME = 'submitted_ip'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD COLUMN `submitted_ip` varchar(45) NOT NULL DEFAULT '''' AFTER `declaration_at`'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := IF(
  EXISTS(SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dryzen_return_schema AND TABLE_NAME = 'oc_return' AND INDEX_NAME = 'invoice_number'),
  'DO 0',
  'ALTER TABLE `oc_return` ADD INDEX `invoice_number` (`invoice_number`)'
);
PREPARE dryzen_return_stmt FROM @dryzen_return_sql;
EXECUTE dryzen_return_stmt;
DEALLOCATE PREPARE dryzen_return_stmt;

SET @dryzen_return_sql := NULL;
SET @dryzen_return_schema := NULL;
SET @dryzen_return_sql_mode := NULL;

-- This legacy modification replaces the entire return controller/model at
-- cache-build time. Its functionality is now maintained in source files, so
-- leaving it enabled would bypass the CSRF, declaration, export and mail code.
UPDATE `oc_modification`
SET `status` = 0
WHERE `code` = 'basel_custom_return_form_oc3';
