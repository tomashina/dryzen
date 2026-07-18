<?php

namespace Agmedia\Api\Models;

use Agmedia\Api\Helper\Helper;
use Agmedia\Helpers\Log;
use Agmedia\Models\Attribute\AttributeDescription;
use Agmedia\Models\Category\CategoryDescription;
use Agmedia\Models\Manufacturer\Manufacturer;
use Agmedia\Models\Product\Product;
use Illuminate\Support\Str;

class OC_Product
{

    /**
     * @param array|null $product
     *
     * @return array
     */
    public static function resolveCategories(array $product = null): array
    {
        $response = [0 => agconf('import.default_category')];

        return $response;
    }


    /**
     * @param string|null $brand
     *
     * @return array
     */
    public static function resolveBrand(string $brand = null): array
    {
        if ($brand) {
            $has = Manufacturer::where('name', $brand)->first();

            if ($has) {
                return [
                    'id' => $has->manufacturer_id,
                    'name' => $has->name
                ];
            }
        }

        return ['id' => 0, 'name' => ''];
    }


    /**
     * @param array $categories
     * @param array $manufacturer
     *
     * @return string
     */
    public static function resolveTags(array $categories, array $manufacturer): string
    {
        $tags_string = '';

        if ( ! empty($categories)) {
            $cats = collect($categories)->take(2);

            foreach ($cats as $cat) {
                $title = CategoryDescription::query()->where('category_id', $cat)->first()->name;

                $title = str_replace(' i ', ',', strtolower($title));
                $title = str_replace(' za ', ',', $title);
                $title = str_replace(' I ', ',', $title);
                $title = str_replace(' ZA ', ',', $title);
                $title = str_replace(', ', ',', $title);
                $arr = explode(',', $title);

                foreach ($arr as $item) {
                    $tags_string .= $item . ',';
                }
            }
        }

        if ( ! empty($manufacturer) && isset($manufacturer['name'])) {
            if ($manufacturer['name'] != 'Razno') {
                $tags_string .= strtolower($manufacturer['name']) . ',';
            }
        }

        return substr($tags_string, 0, -1);
    }


    /**
     *
     * @param array $product
     *
     * @return array
     */
    public static function resolveDescription(array $product): array
    {
        $naziv = $product[1];
        $description = Helper::setDescription($product[19]);

        $response[agconf('import.default_language')] = [
            'name' => Helper::setText($naziv),
            'description' => Helper::setText($description),
            'tag' => '',
            'meta_title' => Helper::setText($naziv),
            'meta_description' => strip_tags(Helper::setText($description)),
            'meta_keyword' => Helper::setText(str_replace(' ', ',', $naziv)),
        ];

        return $response;
    }


    /**
     * @param array $product
     *
     * @return array
     */
    public static function resolveAttributes(array $product): array
    {
        $response = [];

        foreach (agconf('attribute_sync') as $key => $attribute) {
            $has = AttributeDescription::query()->where('name', $attribute)->first();

            if ($has && $has->count() && $product[$key]) {
                $response[] = [
                    'attribute_id' => $has->attribute_id,
                    'product_attribute_description' => [
                        agconf('import.default_language') => [
                            'text' => Helper::setText($product[$key])
                        ]
                    ]
                ];
            }
        }

        return $response;
    }


    /**
     * @param array $product
     *
     * @return array
     */
    public static function resolveGenericAttributes(array $attributes): array
    {
        $response = [];

        if ( ! empty($attributes)) {
            foreach ($attributes as $key => $attribute) {
                if ($key) {
                    $has = AttributeDescription::query()->where('name', $attribute['title'])->first();

                    if ($has && $has->count() && $attribute['value']) {
                        $response[] = [
                            'attribute_id' => $has->attribute_id,
                            'product_attribute_description' => [
                                agconf('import.default_language') => [
                                    'text' => Helper::setText($attribute['value'])
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        return $response;
    }


    /**
     * @param array $product
     *
     * @return string[]
     */
    public static function resolveSeoUrl(array $product): array
    {
        $slug = Str::slug($product[2]) . '-' . $product[1];

        return [
            agconf('import.default_language') => $slug
        ];
    }


    /**
     * @param array $product
     *
     * @return int
     */
    public static function resolveTax(array $product = null): int
    {
        return agconf('import.default_tax_class');
    }


    /**
     * @param array $product
     *
     * @return array|null
     */
    public static function resolveImages(array $product)
    {
        $response = [];

        if (isset($product[4]) && ! empty($product[4])) {
            $uid = $product[0];
            $path = agconf('import.image_path') . 'ideus-' . $uid . '/';

            if ( ! is_dir(DIR_IMAGE . $path)) {
                mkdir(DIR_IMAGE . $path, 0777, true);

                $response[] = [
                    'image' => static::getImagePath($uid, $product[2], $path),
                    'sort_order' => 0
                ];

            } else {
                $has = Product::query()->where('sku', $uid)->first();

                if ($has) {
                    $response[] = [
                        'image' => $has->image,
                        'sort_order' => 0
                    ];
                }
            }

            return $response;
        }

        return null;
    }


    /**
     * @param array $product
     *
     * @return string
     */
    public static function hashProductData(array $product): string
    {
        unset($product['stanje_kol']);

        return sha1(
            collect($product)->toJson()
        );
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param string $product
     * @param string $naziv
     * @param string $folder
     *
     * @return string
     */
    private static function getImagePath(string $product, string $naziv, string $path): string
    {
        if ($product) {
            $name = Str::slug($naziv) . '-' . strtoupper(Str::random(9)) . '.jpg';

            $image = file_get_contents('https://www.ideus.pl/pl/gfx/products/photos/' . $product . '.jpg');

            if ($image) {
                file_put_contents(DIR_IMAGE . $path . $name, $image);

                // Return only the image path.
                return $path . $name;
            }
        }

        return 'not_valid_image';
    }


}