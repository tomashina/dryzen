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
class Ideus
{

    /**
     * @var array|null
     */
    protected $data = null;


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
        $attributes   = OC_Product::resolveAttributes($this->data);
        $description  = OC_Product::resolveDescription($this->data);
        $images       = OC_Product::resolveImages($this->data);
        $stock_status = $this->data[3] ? agconf('import.default_stock_full') : agconf('import.default_stock_empty');
        $status       = 1;

        $description[agconf('import.default_language')]['tag'] = OC_Product::resolveTags($categories, $brand);

        $image_path = $images[0]['image'] ?? agconf('import.image_placeholder');
        unset($images[0]);

        return [
            'model'               => $this->data[0],
            'sku'                 => $this->data[0],
            'upc'                 => '',
            'ean'                 => Helper::setText($this->data[1]),
            'jan'                 => '',
            'isbn'                => '',
            'mpn'                 => '',
            'location'            => '',
            'price'               => 0,
            'tax_class_id'        => OC_Product::resolveTax($this->data),
            'quantity'            => $this->data[3],
            'minimum'             => 1,
            'subtract'            => 1,
            'stock_status_id'     => $stock_status,
            'shipping'            => 1,
            'date_available'      => Carbon::now()->subDay()->format('Y-m-d'),
            'length'              => '',
            'width'               => '',
            'height'              => $this->data[28],
            'length_class_id'     => 1,
            'weight'              => $this->data[29],
            'weight_class_id'     => 1,
            'status'              => $status,
            'sort_order'          => 0,
            //'manufacturer'        => $brand['name'],
            'manufacturer_id'     => $brand['id'],
            'category'            => '',
            'filter'              => '',
            'download'            => '',
            'related'             => '',
            'image'               => $image_path,
            'points'              => '',
            'product_store'       => [0 => 0],
            'product_attribute'   => $attributes,
            'product_description' => $description,
            'product_image'       => $images,
            'product_layout'      => [0 => ''],
            'product_category'    => $categories,
            'product_seo_url'     => [0 => OC_Product::resolveSeoUrl($this->data)],
        ];
    }


    /**
     * @param array|null $data
     *
     * @return void
     */
    public function resolveAttributes(array $data = null): void
    {
        $this->checkData($data);

        $approved_atts = agconf('attribute_sync');

        foreach ($this->data as $key_1 => $attribute) {
            foreach ($approved_atts as $key_2 => $att) {
                if ($key_1 == $key_2) {
                    OC_Attribute::makeAttribute($att);
                }
            }
        }
    }


    /**
     * @return array
     */
    public function getQuantity()
    {
        $response = [];

        foreach ($this->data as $key => $item) {
            if ($key) {
                $response[] = [
                    'sku' => $item[0],
                    'quantity' => $item[3],
                ];
            }
        }

        return $response;
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