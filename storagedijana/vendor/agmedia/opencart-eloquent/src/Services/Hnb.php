<?php


namespace Agmedia\Services;


class Hnb
{
    
    /**
     * @param $currency
     *
     * @return mixed
     */
    public static function getCurrencyValue($currency = 'EUR')
    {
        $response = json_decode(file_get_contents('http://api.hnb.hr/tecajn/v1?valuta=' . $currency));
        
        return str_replace(',', '.', $response[0]->{'Srednji za devize'});
    }
}