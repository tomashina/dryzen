<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Response\Validation;

use Mojakoda\Leanpay\Exception\ResponseStatusValidationException;

interface ResponseStatusValidationInterface
{
    /**
     * @param array $data
     * @throws ResponseStatusValidationException
     */
    public function validate(array $data);
}