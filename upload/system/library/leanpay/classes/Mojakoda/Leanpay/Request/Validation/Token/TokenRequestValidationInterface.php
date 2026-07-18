<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Request\Validation\Token;

use Mojakoda\Leanpay\Exception\TokenRequestValidationException;

interface TokenRequestValidationInterface
{
    /**
     * @param $requestData
     *
     * @throws TokenRequestValidationException
     */
    public function validate($requestData);
}