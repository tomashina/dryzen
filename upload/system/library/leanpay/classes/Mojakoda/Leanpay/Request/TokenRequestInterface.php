<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Request;

interface TokenRequestInterface
{

    /**
     * @param array  $tokenRequestData
     * @param bool   $testMode
     * @param string $country
     *
     * @return $this
     */
    public function execute(array $tokenRequestData, $testMode = FALSE, $country = 'SI');

    /**
     * @return int
     */
    public function getResponseCode();

    /**
     * @return string
     */
    public function getToken();
}
