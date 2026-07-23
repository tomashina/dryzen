-- DryZen / OpenCart 3
-- Završni inkrementalni patch za live:
-- - HR/EN Shop SEO rute i glavni izbornik
-- - footer custom linkovi
-- - nova Women slika na stranici "Znoje mi se pazusi"
-- - uklanjanje zastarjelog OCMOD language redirecta koji pamti prethodnu stranicu
--
-- Skripta je idempotentna i može se sigurno pokrenuti više puta.
-- Projekt koristi prefiks tablica `oc_`; prilagodite ga ako je na live bazi drukčiji.
-- Nakon importa u OpenCart administraciji obavezno pokrenite:
-- Extensions > Modifications > Refresh.

START TRANSACTION;

UPDATE `oc_language`
SET `status` = 1,
    `sort_order` = 2
WHERE `code` = 'en-gb';

DELETE `su`
FROM `oc_seo_url` AS `su`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `su`.`language_id`
WHERE `su`.`store_id` = 0
  AND `su`.`query` = 'category_id=1'
  AND `l`.`code` IN ('hr-hr', 'en-gb');

INSERT INTO `oc_seo_url` (`store_id`, `language_id`, `query`, `keyword`)
SELECT 0, `language_id`, 'category_id=1', 'proizvodi'
FROM `oc_language`
WHERE `code` = 'hr-hr'
LIMIT 1;

INSERT INTO `oc_seo_url` (`store_id`, `language_id`, `query`, `keyword`)
SELECT 0, `language_id`, 'category_id=1', 'shop'
FROM `oc_language`
WHERE `code` = 'en-gb'
LIMIT 1;

UPDATE `oc_mega_menu`
SET `link` = CASE `id`
  WHEN 49 THEN 'a:2:{i:3;s:6:"o-nama";i:1;s:8:"about-us";}'
  WHEN 50 THEN 'a:2:{i:3;s:9:"proizvodi";i:1;s:4:"shop";}'
  WHEN 52 THEN 'a:2:{i:3;s:7:"kontakt";i:1;s:7:"contact";}'
  WHEN 53 THEN 'a:2:{i:3;s:1:"/";i:1;s:1:"/";}'
  ELSE `link`
END
WHERE `id` IN (49, 50, 52, 53);

UPDATE `oc_setting`
SET `value` = JSON_SET(
  `value`,
  '$."1".links."1".target',
  JSON_OBJECT('3', 'opci-uvjeti-koristenja', '1', 'general-terms-of-use'),
  '$."1".links."2".target',
  JSON_OBJECT('3', 'pravila-privatnosti', '1', 'privacy-policy'),
  '$."1".links."3".target',
  JSON_OBJECT('3', 'izjava-o-sigurnosti-online-placanja', '1', 'security-of-online-payments'),
  '$."1".links."4".target',
  JSON_OBJECT('3', 'pravo-i-nacin-podnosenja-prigovora', '1', 'right-to-submit-a-complaint'),
  '$."1".links."5".target',
  JSON_OBJECT('3', 'povrat-robe-zamjena-i-reklamacije', '1', 'returns-exchanges-and-complaints'),
  '$."2".links."1".target',
  JSON_OBJECT('3', 'index.php?route=account/login', '1', 'index.php?route=account/login'),
  '$."2".links."2".target',
  JSON_OBJECT('3', 'index.php?route=account/return/add', '1', 'index.php?route=account/return/add'),
  '$."2".links."3".target',
  JSON_OBJECT('3', 'index.php?route=account/order', '1', 'index.php?route=account/order'),
  '$."2".links."4".target',
  JSON_OBJECT('3', 'kontakt', '1', 'contact')
)
WHERE `store_id` = 0
  AND `code` = 'basel'
  AND `key` = 'basel_footer_columns'
  AND JSON_VALID(`value`) = 1;

UPDATE `oc_module`
SET `setting` = REPLACE(
  `setting`,
  'catalog/need-armpits-2026/women-word.jpg',
  'catalog/need-armpits-2026/dryzen-slike-a-web.png'
)
WHERE `code` = 'basel_content'
  AND `name` = 'DryZen Armpits Products';

-- HuntBee SEO Multi-Language i dalje ostaje aktivan za SEO URL-ove.
-- Uklanjaju se samo dva zastarjela bloka koji su language switch temeljili
-- na prethodno spremljenoj session ruti umjesto na trenutačnoj stranici.
SET @dryzen_ocmod_xml := NULL;

SELECT `xml`
INTO @dryzen_ocmod_xml
FROM `oc_modification`
WHERE `code` = 'huntbee_seo_multi_language_url_ocmod'
LIMIT 1;

SET @dryzen_block_start := LOCATE(
  '<file path="catalog/controller/common/language.php">',
  @dryzen_ocmod_xml
);
SET @dryzen_block_end := LOCATE(
  '</file>',
  @dryzen_ocmod_xml,
  @dryzen_block_start
);
SET @dryzen_ocmod_xml := IF(
  @dryzen_block_start > 0 AND @dryzen_block_end >= @dryzen_block_start,
  INSERT(
    @dryzen_ocmod_xml,
    @dryzen_block_start,
    @dryzen_block_end - @dryzen_block_start + LENGTH('</file>'),
    ''
  ),
  @dryzen_ocmod_xml
);

SET @dryzen_block_start := LOCATE(
  '<file path="catalog/controller/common/home.php">',
  @dryzen_ocmod_xml
);
SET @dryzen_block_end := LOCATE(
  '</file>',
  @dryzen_ocmod_xml,
  @dryzen_block_start
);
SET @dryzen_ocmod_xml := IF(
  @dryzen_block_start > 0 AND @dryzen_block_end >= @dryzen_block_start,
  INSERT(
    @dryzen_ocmod_xml,
    @dryzen_block_start,
    @dryzen_block_end - @dryzen_block_start + LENGTH('</file>'),
    ''
  ),
  @dryzen_ocmod_xml
);

UPDATE `oc_modification`
SET `xml` = @dryzen_ocmod_xml,
    `status` = 1
WHERE `code` = 'huntbee_seo_multi_language_url_ocmod'
  AND @dryzen_ocmod_xml IS NOT NULL;

COMMIT;

SELECT
  `l`.`code` AS `language_code`,
  `su`.`query`,
  `su`.`keyword`
FROM `oc_seo_url` AS `su`
INNER JOIN `oc_language` AS `l`
  ON `l`.`language_id` = `su`.`language_id`
WHERE `su`.`store_id` = 0
  AND `su`.`query` = 'category_id=1'
  AND `l`.`code` IN ('hr-hr', 'en-gb')
ORDER BY `l`.`code`;

SET @dryzen_ocmod_xml := NULL;
SET @dryzen_block_start := NULL;
SET @dryzen_block_end := NULL;
