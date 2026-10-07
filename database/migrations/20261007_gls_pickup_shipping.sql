-- DryZen / OpenCart 3
-- GLS ParcelShop and parcel-locker delivery with sender drop-off at a GLS locker.
-- DryZen stores and charges final prices (config_tax=0 and tax class 0).
-- Official size tiers 6/7/8/10/12 EUR are reduced by the confirmed 1.50 EUR
-- sender drop-off discount, yielding final prices 4.50/5.50/6.50/8.50/10.50 EUR.
-- Current DryZen products have no dimensions, so both methods default to S.
-- The migration is idempotent and preserves any settings already saved in admin.

SET @dryzen_gls_schema := DATABASE();

SET @dryzen_gls_sql := IF(
  EXISTS(
    SELECT 1
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @dryzen_gls_schema
      AND `TABLE_NAME` = 'oc_order'
      AND `COLUMN_NAME` = 'gls_ps'
  ),
  'SELECT ''oc_order.gls_ps already exists'' AS message',
  'ALTER TABLE `oc_order` ADD COLUMN `gls_ps` text NULL AFTER `shipping_code`'
);
PREPARE dryzen_gls_stmt FROM @dryzen_gls_sql;
EXECUTE dryzen_gls_stmt;
DEALLOCATE PREPARE dryzen_gls_stmt;

INSERT INTO `oc_extension` (`type`, `code`)
SELECT 'shipping', 'glsshop'
WHERE NOT EXISTS (
  SELECT 1 FROM `oc_extension` WHERE `type` = 'shipping' AND `code` = 'glsshop'
);

INSERT INTO `oc_extension` (`type`, `code`)
SELECT 'shipping', 'glspaketomat'
WHERE NOT EXISTS (
  SELECT 1 FROM `oc_extension` WHERE `type` = 'shipping' AND `code` = 'glspaketomat'
);

-- Direct SQL registration bypasses OpenCart's installer, so mirror its access
-- and modify grants for the current Administrator group.
UPDATE `oc_user_group`
SET `permission` = JSON_ARRAY_APPEND(`permission`, '$.access', 'extension/shipping/glsshop')
WHERE `user_group_id` = 1
  AND JSON_VALID(`permission`)
  AND NOT JSON_CONTAINS(JSON_EXTRACT(`permission`, '$.access'), JSON_QUOTE('extension/shipping/glsshop'));

UPDATE `oc_user_group`
SET `permission` = JSON_ARRAY_APPEND(`permission`, '$.modify', 'extension/shipping/glsshop')
WHERE `user_group_id` = 1
  AND JSON_VALID(`permission`)
  AND NOT JSON_CONTAINS(JSON_EXTRACT(`permission`, '$.modify'), JSON_QUOTE('extension/shipping/glsshop'));

UPDATE `oc_user_group`
SET `permission` = JSON_ARRAY_APPEND(`permission`, '$.access', 'extension/shipping/glspaketomat')
WHERE `user_group_id` = 1
  AND JSON_VALID(`permission`)
  AND NOT JSON_CONTAINS(JSON_EXTRACT(`permission`, '$.access'), JSON_QUOTE('extension/shipping/glspaketomat'));

UPDATE `oc_user_group`
SET `permission` = JSON_ARRAY_APPEND(`permission`, '$.modify', 'extension/shipping/glspaketomat')
WHERE `user_group_id` = 1
  AND JSON_VALID(`permission`)
  AND NOT JSON_CONTAINS(JSON_EXTRACT(`permission`, '$.modify'), JSON_QUOTE('extension/shipping/glspaketomat'));

INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)
SELECT defaults_to_add.`store_id`, defaults_to_add.`code`, defaults_to_add.`key`, defaults_to_add.`value`, 0
FROM (
  SELECT 0 AS `store_id`, 'shipping_glsshop' AS `code`, 'shipping_glsshop_cost_xs' AS `key`, '4.50' AS `value`
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_cost_s', '5.50'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_cost_m', '6.50'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_cost_l', '8.50'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_cost_xl', '10.50'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_default_size', 'S'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_tax_class_id', '0'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_geo_zone_id', '6'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_status', '1'
  UNION ALL SELECT 0, 'shipping_glsshop', 'shipping_glsshop_sort_order', '3'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_cost_xs', '4.50'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_cost_s', '5.50'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_cost_m', '6.50'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_cost_l', '8.50'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_cost_xl', '10.50'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_default_size', 'S'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_tax_class_id', '0'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_geo_zone_id', '6'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_status', '1'
  UNION ALL SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_sort_order', '2'
) AS defaults_to_add
LEFT JOIN `oc_setting` AS existing_setting
  ON existing_setting.`store_id` = defaults_to_add.`store_id`
  AND existing_setting.`code` = defaults_to_add.`code`
  AND existing_setting.`key` = defaults_to_add.`key`
WHERE existing_setting.`setting_id` IS NULL;

SET @dryzen_gls_sql := NULL;
SET @dryzen_gls_schema := NULL;
