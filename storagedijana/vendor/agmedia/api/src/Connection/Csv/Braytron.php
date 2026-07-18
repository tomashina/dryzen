<?php

namespace Agmedia\Api\Connection\Csv;

use Agmedia\Helpers\Log;

/**
 *
 */
class Braytron
{

    /**
     * @var string
     */
    private $url = 'https://b2b.braytron.com/genel/xml/2159D3B6-3D43-4156-8E86-46083EB9A201';

    /**
     * @var null
     */
    public $xml = null;

    /**
     * @var array
     */
    public $quantity = [];

    /**
     * @var array
     */
    public $images = [];


    /**
     * @param string|null $target
     *
     * @return $this|\$1|false|\SimpleXMLElement|null
     */
    public function getXML(string $target = null)
    {
        $this->xml = simplexml_load_string(file_get_contents($this->url));

        if ($target && $target == 'quantity') {
            $this->resolveQuantity();

            return $this;
        }

        if ($target && $target == 'images') {
            $this->resolveImages();

            return $this;
        }

        return $this->xml;
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @return $this
     */
    private function resolveImages()
    {
        $this->images = [];

        foreach ($this->xml->Stok as $item) {
            $temp_images = [];

            for ($i = 2; $i < 11; $i++) {
                if ((string) $item->{'Image' . $i} != '') {
                    $temp_images[] = (string) $item->{'Image' . $i};
                }
            }

            $this->images[] = [
                'sku' => (string) $item->ProductCode,
                'images' => $temp_images
            ];
        }

        return $this;
    }


    /**
     * @return $this
     */
    private function resolveQuantity()
    {
        $this->quantity = [];

        foreach ($this->xml->Stok as $item) {
            $this->quantity[] = [
                'sku' => (string) $item->ProductCode,
                'quantity' => (string) $item->Quantity
            ];
        }

        return $this;
    }
}