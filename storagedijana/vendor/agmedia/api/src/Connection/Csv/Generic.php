<?php

namespace Agmedia\Api\Connection\Csv;

use Agmedia\Api\Helper\Helper;
use Agmedia\Api\Models\OC_Attribute;
use Agmedia\Api\Models\OC_Product;
use Agmedia\Helpers\Log;
use Illuminate\Support\Carbon;

/**
 *
 */
class Generic
{

    /**
     * @var array|null
     */
    protected ?array $data = null;


    /**
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->data = $data;
    }


    /**
     * @return array
     */
    public function resolveProduct(array $data = null): array
    {
        $this->checkData($data);

        $brand        = OC_Product::resolveBrand('');
        $categories   = OC_Product::resolveCategories($this->data);
        $attributes   = OC_Product::resolveGenericAttributes(isset($this->data['attributes']) ? $this->data['attributes'] : []);
        $description  = OC_Product::resolveDescription($this->data);
        //$images       = OC_Product::resolveImages($this->data);
        $stock_status = 1 ? agconf('import.default_stock_full') : agconf('import.default_stock_empty');
        $status       = 1;

        $description[agconf('import.default_language')]['tag'] = OC_Product::resolveTags($categories, $brand);

        //$image_path = $images[0]['image'] ?? agconf('import.image_placeholder');
        //unset($images[0]);

        return [
            'model'               => $this->data[0],
            'sku'                 => $this->data[0],
            'upc'                 => '',
            'ean'                 => Helper::setText($this->data[8]),
            'jan'                 => '',
            'isbn'                => '',
            'mpn'                 => '',
            'location'            => '',
            'price'               => (float)str_replace(',', '.', $this->data[3]),
            'tax_class_id'        => OC_Product::resolveTax($this->data),
            'quantity'            => 1,
            'minimum'             => 1,
            'subtract'            => 1,
            'stock_status_id'     => $stock_status,
            'shipping'            => 1,
            'date_available'      => Carbon::now()->subDay()->format('Y-m-d'),
            'length'              => '',
            'width'               => '',
            'height'              => $this->data[45],
            'length_class_id'     => 1,
            'weight'              => $this->data[44],
            'weight_class_id'     => 1,
            'status'              => $status,
            'sort_order'          => 0,
            //'manufacturer'        => $brand['name'],
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
            'product_seo_url'     => [0 => OC_Product::resolveSeoUrl($this->data)],
        ];
    }


    /**
     * @param array|null $data
     *
     * @return array
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
                        'value' => $atr[1]
                    ];

                    OC_Attribute::makeAttribute($atr[0]);
                }
            }
        }

        return $res;
    }


    /**
     * @param array|null $data
     *
     * @return void
     */
    private function checkData(array $data = null): void
    {
        if ($data) {
            $this->data = $data;
        }
    }
}