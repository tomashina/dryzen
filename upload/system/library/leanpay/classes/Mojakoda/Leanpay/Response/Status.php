<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Response;

use Mojakoda\Leanpay\Exception\InvalidMd5SignatureException;

class Status
{
    const SUCCESS = 'SUCCESS';
    const FAILED = 'FAILED';
    const CANCELLED = 'CANCELLED';
    const EXPIRED = 'EXPIRED';

    protected $leanPayTransactionId;
    protected $vendorTransactionId;
    protected $amount;
    protected $status;
    protected $md5Signature;

    public function __construct($leanPayTransactionId, $vendorTransactionId, $amount, $status, $md5Signature)
    {
        $this->leanPayTransactionId = $leanPayTransactionId;
        $this->vendorTransactionId = $vendorTransactionId;
        $this->amount = number_format($amount, 2, '.', '');
        $this->status = $status;
        $this->md5Signature = $md5Signature;
    }

    public function getLeanPayTransactionId()
    {
        return $this->leanPayTransactionId;
    }

    public function getVendorTransactionId()
    {
        return $this->vendorTransactionId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getMd5Signature()
    {
        return $this->md5Signature;
    }

    /**
     * @param $secretWord
     * @throws InvalidMd5SignatureException
     */
    public function verifySignature($secretWord)
    {
        if ($this->status != self::SUCCESS) {
            $leanPayTransactionId = 'null';
        } else {
            $leanPayTransactionId = $this->leanPayTransactionId;
        }
        $calculatedSignature = md5(
            $leanPayTransactionId . $this->vendorTransactionId . md5($secretWord) . $this->amount . $this->status
        );
        if ($calculatedSignature !== $this->md5Signature) {
            throw new InvalidMd5SignatureException($this->md5Signature, $calculatedSignature, 'Invalid MD5 signature.');
        }
    }
}