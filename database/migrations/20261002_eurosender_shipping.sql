-- DryZen / OpenCart 3
-- Eurosender shipment state, quote/booking prices, labels and tracking.
-- Booking is intentionally manual: this migration does not register an
-- order-status event because POST /v1/orders can charge the Eurosender account
-- and the API has no documented idempotency key.

CREATE TABLE IF NOT EXISTS `oc_eurosender_shipment` (
  `eurosender_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_number` varchar(64) NOT NULL DEFAULT '',
  `customer_internal_reference` varchar(128) NOT NULL DEFAULT '',
  `service_type` varchar(96) NOT NULL DEFAULT '',
  `environment` varchar(16) NOT NULL DEFAULT 'sandbox',
  `state` varchar(32) NOT NULL DEFAULT '',
  `retryable` tinyint(1) NOT NULL DEFAULT '0',
  `order_code` varchar(128) NULL DEFAULT NULL,
  `status` varchar(96) NOT NULL DEFAULT '',
  `tracking_number` varchar(255) NOT NULL DEFAULT '',
  `tracking_url` varchar(512) NOT NULL DEFAULT '',
  `currency_code` varchar(3) NOT NULL DEFAULT 'EUR',
  `quote_price` decimal(15,4) NULL DEFAULT NULL,
  `booked_price` decimal(15,4) NULL DEFAULT NULL,
  `price_difference` decimal(15,4) NULL DEFAULT NULL,
  `last_http_status` int(11) NOT NULL DEFAULT '0',
  `creation_attempted_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `tracking_checked_at` datetime NULL DEFAULT NULL,
  `label_downloaded_at` datetime NULL DEFAULT NULL,
  `creation_error` text NULL,
  `tracking_error` text NULL,
  `label_error` text NULL,
  `payload` mediumtext NULL,
  `quote_response` mediumtext NULL,
  `validation_response` mediumtext NULL,
  `response` mediumtext NULL,
  `tracking_response` mediumtext NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`eurosender_shipment_id`),
  UNIQUE KEY `order_id` (`order_id`),
  UNIQUE KEY `order_code` (`order_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Keep the migration safe if an earlier draft created the table before the
-- environment column was introduced.
SET @eurosender_environment_column_exists := (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'oc_eurosender_shipment'
    AND column_name = 'environment'
);
SET @eurosender_environment_sql := IF(
  @eurosender_environment_column_exists = 0,
  'ALTER TABLE `oc_eurosender_shipment` ADD `environment` varchar(16) NOT NULL DEFAULT ''sandbox'' AFTER `service_type`',
  'DO 0'
);
PREPARE eurosender_environment_statement FROM @eurosender_environment_sql;
EXECUTE eurosender_environment_statement;
DEALLOCATE PREPARE eurosender_environment_statement;
