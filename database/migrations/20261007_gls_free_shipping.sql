-- GLS ParcelShop and parcel-locker shipping becomes free from a 50.00 EUR
-- product subtotal. The insert is idempotent so it is safe to re-run.

INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)
SELECT defaults_to_add.`store_id`, defaults_to_add.`code`, defaults_to_add.`key`, defaults_to_add.`value`, 0
FROM (
  SELECT 0 AS `store_id`, 'shipping_glsshop' AS `code`, 'shipping_glsshop_free_total' AS `key`, '50.00' AS `value`
  UNION ALL
  SELECT 0, 'shipping_glspaketomat', 'shipping_glspaketomat_free_total', '50.00'
) AS defaults_to_add
LEFT JOIN `oc_setting` AS existing_setting
  ON existing_setting.`store_id` = defaults_to_add.`store_id`
  AND existing_setting.`code` = defaults_to_add.`code`
  AND existing_setting.`key` = defaults_to_add.`key`
WHERE existing_setting.`setting_id` IS NULL;
