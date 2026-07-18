<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\Leanpay\Request;

class TokenRequest implements TokenRequestInterface
{
    protected $responseCode;
    protected $token;

    public function execute(array $tokenRequestData, $testMode = false, $country = 'SI')
    {
        $this->resetData();
        $options = [
            'http' => [
                'header'  => 'Content-Type: application/json',
                'method'  => 'POST',
                'content' => json_encode($tokenRequestData)
            ]
        ];
        $context  = stream_context_create($options);
        $result = @file_get_contents(Endpoint::getTokenUrl($testMode, $country), false, $context);

        $statusLine = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
        $this->responseCode = (int)$match[1];
        if ($this->responseCode === 200) {
            $this->token = json_decode($result, true)['token'];
        }

        return $this;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function resetData()
    {
        $this->responseCode = null;
        $this->token = null;
    }
}
