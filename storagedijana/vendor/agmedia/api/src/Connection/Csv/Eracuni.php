<?php

namespace Agmedia\Api\Connection\Csv;

use Agmedia\Api\Helper\Helper;
use Agmedia\Api\Models\OC_Attribute;
use Agmedia\Api\Models\OC_Product;
use Agmedia\Models\Order\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Klasa za pripremu podataka (proizvodi, narudžbe/ponude) za e-Računi API.
 *
 * Pravila:
 * - Da totals budu 1:1 kao u OpenCartu, u Items šaljemo stvarnu neto cijenu iz narudžbe.
 * - Katalog koristimo samo da artikl postoji (grossPrice u katalogu držimo 0.00).
 * - Dostava ide kao artikl DOSTAVA (services) i cijenu šaljemo u Items.
 */
class Eracuni
{
    /**
     * @var array|null
     */
    protected $data = null;

    /**
     * Globalni rate-limit state (da ne radimo burst).
     */
    private $lastApiCallAt = 0.0;

    /**
     * Minimalni razmak između poziva (ms).
     * Ako i dalje dobivaš 429, digni na 1500-2000.
     */
    private $minIntervalMs = 1200;

    /**
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->data = $data;
    }

    /**
     * Normalizira podatke o proizvodu (import proizvoda iz e-Računi kataloga u OC).
     */
    public function resolveProduct(array $data = null): array
    {
        $this->checkData($data);

        $brand        = OC_Product::resolveBrand();
        $categories   = OC_Product::resolveCategories();
        $attributes   = OC_Product::resolveGenericAttributes(isset($this->data['attributes']) ? $this->data['attributes'] : []);
        $description  = Helper::resolveDescription($this->data['name'], $this->data['description']);
        $stock_status = 1 ? agconf('import.default_stock_full') : agconf('import.default_stock_empty');
        $status       = 1;

        $description[agconf('import.default_language')]['tag'] = OC_Product::resolveTags($categories, $brand);

        return [
            'model'               => $this->data['productCode'],
            'sku'                 => $this->data['productCode'],
            'upc'                 => '',
            'ean'                 => Helper::setText($this->data['barCode']),
            'jan'                 => '',
            'isbn'                => '',
            'mpn'                 => '',
            'location'            => '',
            'price'               => (float) str_replace(',', '.', $this->data['grossPrice']),
            'tax_class_id'        => OC_Product::resolveTax(),
            'quantity'            => 1,
            'minimum'             => 1,
            'subtract'            => 1,
            'stock_status_id'     => $stock_status,
            'shipping'            => 1,
            'date_available'      => Carbon::now()->subDay()->format('Y-m-d'),
            'length'              => '',
            'width'               => '',
            'height'              => '',
            'length_class_id'     => 1,
            'weight'              => '',
            'weight_class_id'     => 1,
            'status'              => $status,
            'sort_order'          => 0,
            'manufacturer_id'     => $brand['id'],
            'category'            => '',
            'filter'              => '',
            'download'            => '',
            'related'             => '',
            'image'               => agconf('import.image_placeholder'),
            'points'              => '',
            'product_store'       => [0 => 0],
            'product_attribute'   => $attributes,
            'product_description' => $description,
            'product_image'       => [],
            'product_layout'      => [0 => ''],
            'product_category'    => $categories,
            'product_seo_url'     => [0 => Helper::resolveSeoUrl($this->data['name'])],
        ];
    }

    /**
     * Kreira payload za narudžbu/ponudu.
     *
     * @param string $type 'order' | 'offer'
     * @param string $mode 'json' | 'form'
     * @return array|string
     */
    public function createSale(string $type = 'order', string $mode = 'json')
    {
        $apiTransactionId = $this->data['order_id'] . '-' . Str::random(9);
        $rootKey = ($type === 'order') ? 'SalesOrder' : 'SalesQuote';

        $sale = $this->getSale();

        if ($mode === 'json') {
            return [
                'sendIssuedInvoiceByEmail' => true,
                'apiTransactionId'         => $apiTransactionId,
                $rootKey                   => $sale,
            ];
        }

        if ($mode === 'form') {
            $data  = 'apiTransactionId="' . $apiTransactionId . '"&sendIssuedInvoiceByEmail=true';
            $data .= '&' . $rootKey . '=' . json_encode($sale, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return $data;
        }

        return [
            'sendIssuedInvoiceByEmail' => true,
            'apiTransactionId'         => $apiTransactionId,
            $rootKey                   => $sale,
        ];
    }

    /**
     * Spremi broj dokumenta u narudžbu.
     */
    public function saveResponse(string $type, array $response, $order_id): void
    {
        if (!isset($response['number'])) return;

        $data = ($type === 'order')
            ? ['number_order' => $response['number']]
            : ['number_quote' => $response['number']];

        Order::query()->where('order_id', $order_id)->update($data);
    }

    /**
     * Grade “sale” segment s ispravnim formatima.
     */
    private function getSale(): array
    {
        $company = json_decode($this->data['custom_field'] ?? '', true) ?: [];
        if (!is_array($company)) $company = [];

        // VAT → country
        $buyerCountry = 'HR';
        $country      = 'HR';

        if (!empty($company[2]) && is_string($company[2])) {
            $vat = strtoupper($company[2]);
            if (strpos($vat, 'SI') === 0) {
                $buyerCountry = 'SI';
                $country      = 'SI';
            } elseif (preg_match('/^\d{11}$/', $vat)) {
                $buyerCountry = 'HR';
                $country      = 'HR';
            }
        }

        // buyerName mora uvijek postojati
        $buyerName = trim((string) ($company[1] ?? ''));
        if ($buyerName === '') {
            $buyerName = trim(($this->data['payment_firstname'] ?? '') . ' ' . ($this->data['payment_lastname'] ?? ''));
        }
        if ($buyerName === '') {
            $buyerName = trim((string) ($this->data['firstname'] ?? '') . ' ' . (string) ($this->data['lastname'] ?? ''));
        }
        if ($buyerName === '') {
            $buyerName = 'Kupac';
        }

        return [
            'vatTransactionType' => '0',
            'buyerTaxNumber'     => $company[2] ?? '',
            'buyerName'          => $buyerName,
            'buyerFirstName'     => $this->data['payment_firstname'] ?? '',
            'buyerLastName'      => $this->data['payment_lastname'] ?? '',
            'buyerStreet'        => $this->data['payment_address_1'] ?? '',
            'buyerPostalCode'    => $this->data['payment_postcode'] ?? '',
            'buyerCity'          => $this->data['payment_city'] ?? '',
            'buyerCountry'       => $buyerCountry,
            'buyerEMail'         => $this->data['email'] ?? '',
            'buyerPhone'         => $this->data['telephone'] ?? '',
            'validUntil'         => Carbon::now()->addDays(7)->format('Y-m-d'),
            'methodOfPayment'    => $this->getSaleMethodOfPayment(),
            'country'            => $country,
            'Items'              => $this->getSaleItems(),
            'Address'            => $this->getSaleAddress($company, $country),
        ];
    }

    /**
     * Payload used only when an order contains a product that does not yet
     * exist in e-Racuni. Existing catalogue records are never overwritten by
     * order submission, so their price and stock-management setup stay intact.
     */
    public function buildCatalogueProduct(array $orderProduct): array
    {
        $code = (string) ($orderProduct['model'] ?? $orderProduct['productCode'] ?? '');
        $name = (string) ($orderProduct['name'] ?? $code);

        return [
            'productCode' => $code,
            'name'        => $name,
            'status'      => 'active',
            'type'        => 'goodsWithoutStockManagement',
            'unit'        => 'piece',
            'grossPrice'  => $this->money(0),
            'barCode'     => (string) ($orderProduct['ean'] ?? $orderProduct['barcode'] ?? ''),
            'description' => $name,
            'allowChangeOfPriceOnTheInvoice'              => true,
            'allowChangeOfProductDescriptionOnTheInvoice' => true,
        ];
    }

    /**
     * Ensure all order products and DOSTAVA exist in the catalogue.
     *
     * One ProductList request replaces N ProductGetByCode requests. Only
     * missing products are imported; existing products are deliberately left
     * untouched because e-Racuni is the source of truth for price and stock.
     */
    public function ensureCatalogueProductsExist(\Agmedia\Api\Api $api, array $auth): void
    {
        $products = [];

        foreach (($this->data['products'] ?? []) as $p) {
            $code = (string) ($p['model'] ?? '');
            if ($code === '' || isset($products[$code])) continue;
            $products[$code] = $p;
        }

        $codes = array_keys($products);
        $codes[] = 'DOSTAVA';

        $listResponse = $this->apiPostWithRetry($api, [
            'username'   => $auth['username'],
            'secretKey'  => $auth['secretKey'],
            'token'      => $auth['token'],
            'method'     => 'ProductList',
            'parameters' => [
                'productCode' => implode(',', $codes),
            ],
        ], 'json');

        if ($listResponse === false || $listResponse === null) {
            throw new \RuntimeException('Ne mogu provjeriti e-Računi katalog zbog greške u komunikaciji.');
        }

        if ($this->isApiError($listResponse)) {
            $description = $this->apiDescription($listResponse) ?: 'Nepoznata greška';
            throw new \RuntimeException('Ne mogu provjeriti e-Računi katalog: ' . $description);
        }

        if (!is_array($listResponse)) {
            throw new \RuntimeException('e-Računi ProductList nije vratio ispravan odgovor.');
        }

        $catalogueProducts = [];
        $this->collectCatalogueProducts($listResponse, $catalogueProducts);
        $existing = array_fill_keys(array_keys($catalogueProducts), true);

        // The order payload deliberately sends the actual OpenCart line price
        // so discounts and historical order totals remain unchanged. Existing
        // e-Racuni catalogue items can reject that price unless this flag is
        // enabled. Update only that permission; never overwrite catalogue
        // prices, stock settings or other product data.
        foreach ($catalogueProducts as $code => $catalogueProduct) {
            if ($code !== 'DOSTAVA' && !isset($products[$code])) continue;
            if (!array_key_exists('allowChangeOfPriceOnTheInvoice', $catalogueProduct)) continue;
            if (!$this->isFalseBoolean($catalogueProduct['allowChangeOfPriceOnTheInvoice'])) continue;

            $fallbackName = $code === 'DOSTAVA'
                ? 'Dostava'
                : (string) ($products[$code]['name'] ?? $code);
            $name = trim((string) ($catalogueProduct['name'] ?? $fallbackName));

            if ($name === '') $name = $fallbackName;

            $this->enableLinePriceChange($api, $auth, $code, $name);
        }

        foreach ($products as $code => $p) {
            if (isset($existing[$code])) continue;

            $payload = $this->buildCatalogueProduct($p);

            $resp = $this->apiPostWithRetry($api, [
                'username'   => $auth['username'],
                'secretKey'  => $auth['secretKey'],
                'token'      => $auth['token'],
                'method'     => 'ProductImport',
                'parameters' => [
                    'importType' => 'createOrUpdate',
                    'product'    => $payload,
                ],
            ], 'json');

            if ($this->isApiError($resp)) {
                $desc = $this->apiDescription($resp) ?: 'Nepoznata greška';
                throw new \RuntimeException("Ne mogu upisati artikl u e-Računi katalog ($code): $desc");
            }
        }

        if (!isset($existing['DOSTAVA'])) {
            $this->upsertServiceProduct($api, $auth, 'DOSTAVA', 'Dostava', 'Trošak dostave');
        }
    }

    private function collectCatalogueProducts($node, array &$products): void
    {
        if (!is_array($node)) return;

        if (isset($node['productCode']) && is_scalar($node['productCode'])) {
            $code = trim((string) $node['productCode']);

            if ($code !== '') {
                if (!isset($products[$code])) $products[$code] = [];

                foreach (['name', 'allowChangeOfPriceOnTheInvoice'] as $key) {
                    if (array_key_exists($key, $node)) {
                        $products[$code][$key] = $node[$key];
                    }
                }
            }
        }

        foreach ($node as $value) {
            if (is_array($value)) $this->collectCatalogueProducts($value, $products);
        }
    }

    private function enableLinePriceChange(\Agmedia\Api\Api $api, array $auth, string $code, string $name): void
    {
        $resp = $this->apiPostWithRetry($api, [
            'username'   => $auth['username'],
            'secretKey'  => $auth['secretKey'],
            'token'      => $auth['token'],
            'method'     => 'ProductUpdate',
            'parameters' => [
                'product' => [
                    'productCode'                     => $code,
                    'name'                            => $name,
                    'allowChangeOfPriceOnTheInvoice' => true,
                ],
            ],
        ], 'json');

        if ($this->isApiError($resp)) {
            $description = $this->apiDescription($resp) ?: 'Nepoznata greška';
            throw new \RuntimeException(
                "Ne mogu omogućiti promjenu cijene na dokumentu za e-Računi artikl ($code): $description"
            );
        }
    }

    private function isFalseBoolean($value): bool
    {
        if ($value === false || $value === 0 || $value === '0') return true;

        return is_string($value) && strtolower(trim($value)) === 'false';
    }

    private function upsertServiceProduct(\Agmedia\Api\Api $api, array $auth, string $code, string $name, string $desc): void
    {
        $payload = [
            'productCode' => $code,
            'name'        => $name,
            'description' => $desc,
            'status'      => 'active',
            'type'        => 'services',
            'unit'        => 'service',
            'grossPrice'  => $this->money(0),
            'currency'    => 'EUR',
            'allowChangeOfPriceOnTheInvoice'              => true,
            'allowChangeOfProductDescriptionOnTheInvoice' => true,
        ];

        $resp = $this->apiPostWithRetry($api, [
            'username'   => $auth['username'],
            'secretKey'  => $auth['secretKey'],
            'token'      => $auth['token'],
            'method'     => 'ProductImport',
            'parameters' => [
                'importType' => 'createOrUpdate',
                'product'    => $payload,
            ],
        ], 'json');

        if ($this->isApiError($resp)) {
            $d = $this->apiDescription($resp) ?: 'Nepoznata greška';
            throw new \RuntimeException("Ne mogu upisati servisni artikl $code: $d");
        }
    }

    /**
     * Metoda plaćanja mapirana iz payment_code.
     */
    private function getSaleMethodOfPayment(): string
    {
        if (($this->data['payment_code'] ?? null) == 'cod') {
            return 'Cash';
        }
        if (($this->data['payment_code'] ?? null) == 'bank_transfer') {
            return 'BankTransfer';
        }
        return 'CreditCard';
    }

    /**
     * Items — šaljemo productCode, quantity i stvarni netPrice iz narudžbe,
     * + DOSTAVA.
     */
    private function getSaleItems(): array
    {
        $vatPercent = 25.0;
        $vatFactor  = 1 + ($vatPercent / 100);

        $items = [];

        $getGrossUnit = function (array $p): float {
            // ako već ima bruto
            if (isset($p['price_gross'])) return (float) $p['price_gross'];
            if (isset($p['grossPrice']))  return (float) $p['grossPrice'];

            // tipično OC: price (net) + tax (po komadu)
            if (isset($p['price']) && isset($p['tax'])) {
                return (float) $p['price'] + (float) $p['tax'];
            }

            // fallback: pretpostavi da je price bruto
            return (float) ($p['price'] ?? 0);
        };

        // 1) Proizvodi
        foreach (($this->data['products'] ?? []) as $p) {
            $code = (string) ($p['model'] ?? '');
            if ($code === '') continue;

            $qty = (int) ($p['quantity'] ?? 1);
            if ($qty < 1) $qty = 1;

            $grossUnit = $getGrossUnit($p);
            $netUnit = $this->resolveProductNetUnitPrice($p, $qty, $grossUnit / $vatFactor);

            $items[] = [
                'productCode' => $code,
                'quantity'    => $qty,
                'netPrice'    => $netUnit,
                'vatPercent'  => $vatPercent,
            ];
        }

        // 2) Dostava
        // OpenCart shipping total je neto iznos, pa ga šaljemo direktno kao netPrice.
        $shippingNet = (float) $this->getShippingTotal();
        if ($shippingNet > 0) {
            $items[] = [
                'productCode' => 'DOSTAVA',
                'quantity'    => 1,
                'netPrice'    => $this->decimal($shippingNet, 2),
                'vatPercent'  => $vatPercent,
            ];
        }

        return $items;
    }

    private function resolveProductNetUnitPrice(array $product, int $quantity, float $fallback): string
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        if (isset($product['total']) && is_numeric($product['total'])) {
            return $this->decimal(((float) $product['total']) / $quantity, 6);
        }

        if (isset($product['price']) && is_numeric($product['price'])) {
            return $this->decimal((float) $product['price'], 6);
        }

        return $this->decimal($fallback, 6);
    }

    private function getShippingTotal(): float
    {
        foreach (($this->data['totals'] ?? []) as $t) {
            if (($t['code'] ?? null) === 'shipping') {
                return (float) ($t['value'] ?? 0);
            }
        }

        if (isset($this->data['shipping_cost'])) {
            return (float) $this->data['shipping_cost'];
        }

        return 0.0;
    }

    /**
     * Final total iz OpenCart totals (code = 'total'), fallback na $this->data['total'].
     */
    private function getOrderTotal(): float
    {
        foreach (($this->data['totals'] ?? []) as $t) {
            if (($t['code'] ?? null) === 'total') {
                return $this->money((float) ($t['value'] ?? 0));
            }
        }
        return $this->money((float) ($this->data['total'] ?? 0));
    }

    /**
     * Money helper: uvijek 2 decimale (float).
     */
    private function money($value): float
    {
        return (float) number_format((float) $value, 2, '.', '');
    }

    private function decimal($value, int $scale = 6): string
    {
        return number_format((float) $value, $scale, '.', '');
    }

    /**
     * Adresa dostave.
     */
    private function getSaleAddress(array $company, string $countryCode = 'HR'): array
    {
        return [
            'firstAddressLine' => $company[1] ?? '',
            'street'           => $this->data['shipping_address_1'] ?? '',
            'postalCode'       => $this->data['shipping_postcode'] ?? '',
            'city'             => $this->data['shipping_city'] ?? '',
            'country'          => $countryCode,
            'type'             => 'Delivery',
        ];
    }

    /**
     * Parsiranje custom atributa.
     */
    public function resolveAttributes(array $data = null, string $param = null): array
    {
        $this->checkData($data);

        $res = [];
        $arr = preg_split('/\n|\r\n?/', $data[$param]);

        for ($i = 0; $i < count($arr); $i++) {
            if ($i) {
                $atr = explode(':', $arr[$i]);

                if (isset($atr[0]) && isset($atr[1])) {
                    $res[] = [
                        'title' => $atr[0],
                        'value' => $atr[1],
                    ];

                    OC_Attribute::makeAttribute($atr[0]);
                }
            }
        }

        return $res;
    }

    /**
     * Batch query pomoćne metode.
     */
    public function getQuantityUpdateQuary(array $data = null): array
    {
        if (!$data) return [];

        $count = 1;
        $query = '';

        foreach ($data as $item) {
            $query .= '("' . $item['StockQuantityInfo']['productCode'] . '", ' . (int) $item['StockQuantityInfo']['quantityOnStock'] . ', 0),';
            $count++;
        }

        return [
            'query' => $query,
            'count' => $count,
        ];
    }

    public function getPriceUpdateQuary(array $data = null): array
    {
        if (!$data) return [];

        $count = 1;
        $query = '';

        foreach ($data as $item) {
            $query .= '("' . $item['productCode'] . '", 0, ' . $this->money((float) $item['grossPrice']) . '),';
            $count++;
        }

        return [
            'query' => $query,
            'count' => $count,
        ];
    }

    public function getNameUpdateQuary(array $data = null): array
    {
        if (!$data) return [];

        $count = 1;
        $query = '';

        foreach ($data as $item) {
            $replaced = str_replace('"', '', $item['name']);
            $query .= '("' . $item['productCode'] . '","' . $replaced . '"),';
            $count++;
        }

        return [
            'query' => $query,
            'count' => $count,
        ];
    }

    /**
     * Prepoznaj error response iz e-Računi wrappera.
     */
    private function isApiError($resp): bool
    {
        return is_array($resp) && (
            (isset($resp['status']) && $resp['status'] === 'error') ||
            (isset($resp['response']['status']) && $resp['response']['status'] === 'error')
        );
    }

    /**
     * Izvuci "description" iz response-a.
     */
    private function apiDescription($resp): string
    {
        $description = (string) ($resp['description'] ?? ($resp['response']['description'] ?? ''));

        if (stripos($description, 'Invalid web services token') !== false) {
            return 'API token nije važeći. Kopirajte aktualni token iz Postavke > Postavke tvrtke > API Web services i ažurirajte upload/env.php.';
        }

        if (stripos($description, 'korisničko ime ili zaporka') !== false || stripos($description, 'username or password') !== false) {
            return 'API korisničko ime ili tajni ključ nisu valjani. U upload/env.php polje password mora sadržavati Secret key API korisnika, a ne lozinku za običnu prijavu.';
        }

        return $description;
    }

    /**
     * True ako je 429.
     */
    private function isTooManyRequests($resp): bool
    {
        $desc = $this->apiDescription($resp);
        return $desc !== '' && stripos($desc, 'Too Many Requests') !== false;
    }

    /**
     * Globalni throttle: osigurava minimalni razmak između API poziva.
     */
    private function rateLimit(): void
    {
        $now = microtime(true);

        if ($this->lastApiCallAt > 0) {
            $elapsedMs = (int) round(($now - $this->lastApiCallAt) * 1000);
            $sleepMs   = $this->minIntervalMs - $elapsedMs;

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        }

        $this->lastApiCallAt = microtime(true);
    }

    /**
     * POST s retry/backoff na 429.
     */
    private function apiPostWithRetry(\Agmedia\Api\Api $api, array $payload, string $format = 'json', int $maxAttempts = 10)
    {
        $attempt   = 0;
        $backoffMs = 1500; // start 1.5s

        while (true) {
            $attempt++;

            $this->rateLimit();

            $resp = $api->post('WebServices/API', $payload, $format);

            if (!$this->isApiError($resp)) {
                return $resp;
            }

            if (!$this->isTooManyRequests($resp)) {
                return $resp;
            }

            if ($attempt >= $maxAttempts) {
                return $resp;
            }

            // backoff + jitter
            $jitter = random_int(0, 400);
            usleep(($backoffMs + $jitter) * 1000);

            $backoffMs = (int) min((int) round($backoffMs * 1.7), 20000);
        }
    }

    /**
     * @param array|null $data
     */
    private function checkData(array $data = null): void
    {
        if ($data) {
            $this->data = $data;
        }
    }
}
