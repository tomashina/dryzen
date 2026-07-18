<?php

namespace Agmedia\Api\Connection\Csv;

use Agmedia\Helpers\Log;

/**
 *
 */
class Dpm
{

    /**
     * @var string
     */
    private $url = 'https://dpm.eu/XML/gJYPNeSX3XY9TPzct8pGBpK4/DPM.xml';

    /**
     * @var null
     */
    public $xml = null;

    /**
     * @var array
     */
    public $quantity = [];




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

       

        return $this->xml;
    }

 


    /**
     * @return $this
     */
    private function resolveQuantity()
    {
        $this->quantity = [];

        foreach ($this->xml->PRODUCT as $item) {
            $this->quantity[] = [
                'sku' => (string) $item->ean,
                'quantity' => empty((string) $item->BOX_QTY) ? 0 : (int) $item->BOX_QTY
            ];
        }

        return $this;
    }
}