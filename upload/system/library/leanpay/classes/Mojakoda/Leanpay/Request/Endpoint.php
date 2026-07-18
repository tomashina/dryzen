<?php

namespace Mojakoda\Leanpay\Request;

class Endpoint
{
    const TEST_URL = 'https://lapp.leanpay.';
    const LIVE_URL = 'https://app.leanpay.';

    /**
     * Retrieves LeanPay token url
     *
     * @param bool $testMode Flag to retrieve test or live URL
     *
     * @return string
     */
    public static function getTokenUrl($testMode = false, $country = 'SI') {
        return self::buildBaseUrl($testMode, $country) . 'vendor/token';
    }

    /**
     * Retrieves LeanPay checkout url
     *
     * @param bool $testMode Flag to retrieve test or live URL
     *
     * @return string
     */
    public static function getCheckoutUrl($token, $testMode = false, $country = 'SI') {
        return self::buildBaseUrl($testMode, $country) . 'vendor/checkout?token=' . $token;
    }

    protected static function buildBaseUrl($testMode = false, $country = 'SI')
    {
        $url = $testMode ? self::TEST_URL : self::LIVE_URL;

        return $url . strtolower($country) . '/';
    }
}
