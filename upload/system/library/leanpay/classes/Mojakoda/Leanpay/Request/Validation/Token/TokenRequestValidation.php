<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Request\Validation\Token;

use Mojakoda\Leanpay\Exception\TokenRequestValidationException;

class TokenRequestValidation implements TokenRequestValidationInterface
{
    protected $requiredFields = [
        'vendorApiKey',
        'vendorTransactionId',
        'amount',
        'successUrl',
        'errorUrl',
        'vendorFirstName',
        'vendorLastName',
        'vendorAddress',
        'vendorZip',
        'vendorCity'
    ];

    public function validate($requestData)
    {
        foreach ($this->requiredFields as $requiredField) {
            if (!array_key_exists($requiredField, $requestData)) {
                throw new TokenRequestValidationException('Required field ' . $requiredField . ' is missing in request data.');
            }
            if (empty(trim($requestData[$requiredField]))) {
                throw new TokenRequestValidationException('Required field ' . $requiredField . ' cannot be empty.');
            }
        }
    }
}