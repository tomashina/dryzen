<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Response\Validation;


use Mojakoda\Leanpay\Exception\ResponseStatusValidationException;

class ResponseStatusValidation implements ResponseStatusValidationInterface
{
    protected $requiredFields = [
        'leanPayTransactionId',
        'vendorTransactionId',
        'amount',
        'status',
        'md5Signature'
    ];

    /**
     * @param array $data
     * @throws ResponseStatusValidationException
     */
    public function validate(array $data)
    {
        foreach ($this->requiredFields as $requiredField) {
            if (!array_key_exists($requiredField, $data)) {
                throw new ResponseStatusValidationException(sprintf('"%s" property is missing in data.', $requiredField));
            }
        }
    }
}