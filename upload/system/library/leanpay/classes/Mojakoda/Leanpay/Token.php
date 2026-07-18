<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay;

use Mojakoda\Leanpay\Exception\TokenRequestException;
use Mojakoda\Leanpay\Exception\TokenRequestValidationException;
use Mojakoda\Leanpay\Request\TokenRequestInterface;
use Mojakoda\Leanpay\Request\Validation\Token\TokenRequestValidationInterface;

class Token
{
    /**
     * @var TokenRequestValidationInterface
     */
    protected $validator;

    /**
     * @var TokenRequestInterface
     */
    protected $request;

    /**
     * @var bool
     */
    protected $testMode;

    /**
     * @var string
     */
    protected $country;

    public function __construct(
        TokenRequestValidationInterface $validator,
        TokenRequestInterface $tokenRequest,
        $testMode = false,
        $country = 'SI'
    ) {
        $this->validator = $validator;
        $this->request = $tokenRequest;
        $this->testMode = $testMode;
        $this->country = $country;
    }

    /**
     * @param $requestData
     *
     * @throws TokenRequestException
     * @throws TokenRequestValidationException
     *
     * @return string
     */
    public function get($requestData)
    {
        $this->validator->validate($requestData);
        if ($this->request->execute($requestData, $this->testMode, $this->country)->getResponseCode() !== 200) {
            throw new TokenRequestException('Request failed to complete successfully.', $this->request->getResponseCode());
        } else {
            return $this->request->getToken();
        }
    }
}
