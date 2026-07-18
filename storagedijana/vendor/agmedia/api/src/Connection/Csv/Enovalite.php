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
class Enovalite
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


        unset($images[0]);

        return [
            'sku'                 => $this->data[0],
            'quantity'            => $this->data[1],

        ];
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
                    'quantity' => $item[1],
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