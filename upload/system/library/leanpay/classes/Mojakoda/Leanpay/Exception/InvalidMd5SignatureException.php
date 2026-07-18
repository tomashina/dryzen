<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Exception;

use Exception;
use Throwable;

class InvalidMd5SignatureException extends Exception
{
    protected $receivedSignature;
    protected $calculatedSignature;

    public function __construct($receivedSignature, $calculatedSignature, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->receivedSignature = $receivedSignature;
        $this->calculatedSignature = $calculatedSignature;
    }

    public function getReceivedSignature()
    {
        return $this->receivedSignature;
    }

    public function getCalculatedSignature()
    {
        return $this->calculatedSignature;
    }
}