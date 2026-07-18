<?php

namespace Agmedia\Api;

use Agmedia\Helpers\Log;

class Api
{
    private $username;
    private $password;
    private $token;
    private $url;

    public function __construct()
    {
        $this->username = agconf('import.api.username');
        $this->password = agconf('import.api.password');
        $this->token    = agconf('import.api.token');
        $this->url      = rtrim(agconf('import.api.url'), '/') . '/';
    }

    /**
     * GET zahtjev.
     * $data može biti array (pretvara se u query string) ili string (dodaje se raw iza ?).
     */
    public function get(string $endpoint, $data = null, array $extraHeaders = [])
    {
        try {
            $url = $this->url . ltrim($endpoint, '/');

            if ($data !== null && $data !== '') {
                if (is_array($data)) {
                    $qs  = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
                    $url .= (strpos($url, '?') === false ? '?' : '&') . $qs;
                } else {
                    // raw string (već formiran query)
                    $url .= (strpos($url, '?') === false ? '?' : '&') . ltrim((string) $data, '?&');
                }
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => $this->resolveApiPassword(),
                CURLOPT_HTTPHEADER     => $this->resolveHeaders('json', $extraHeaders), // Accept: application/json
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $errno    = curl_errno($ch);
            $error    = curl_error($ch);
            $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $this->log('GET ' . $endpoint, $url);
            $this->log('GET ' . $endpoint . ' RESP', (string) $response);

            if ($errno) {
                $this->log('GET ' . $endpoint . ' ERR', $error);
                return false;
            }
            if ($code >= 400) {
                $this->log('GET ' . $endpoint . ' HTTP', 'HTTP ' . $code);
                // možeš baciti exception ili vratiti raw; ja vraćam parsed/false
            }

            return $this->resolveResponse($response);

        } catch (\Exception $exception) {
            $this->log('GET ' . $endpoint . ' EX', $exception->getMessage());
            return false;
        }
    }

    /**
     * POST zahtjev.
     * $body može biti array ili string. Ako $headers_type === 'json' i $body je array → json_encode.
     * $headers_type: 'json' | 'form' | 'xml'
     */
    public function post(string $endpoint, $body, string $headers_type = 'form', array $extraHeaders = [])
    {
        try {
            $url = $this->url . ltrim($endpoint, '/');

            $httpHeaders = $this->resolveHeaders($headers_type, $extraHeaders);

            // Priprema tijela prema tipu
            if ($headers_type === 'json') {
                if (is_array($body)) {
                    $body = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
                } // ako je string, pretpostavljamo da je već JSON
            } elseif ($headers_type === 'form') {
                if (is_array($body)) {
                    $body = http_build_query($body, '', '&', PHP_QUERY_RFC3986);
                } // ako je string, pretpostavljamo da je već form-url-encoded
            }
            // za XML ne diramo

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => 1,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_USERPWD        => $this->resolveApiPassword(),
                CURLOPT_HTTPHEADER     => $httpHeaders,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $errno    = curl_errno($ch);
            $error    = curl_error($ch);
            $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $this->log('POST ' . $endpoint . ' REQ', is_string($body) ? $body : '[binary]');
            $this->log('POST ' . $endpoint . ' RESP', (string) $response);

            if ($errno) {
                $this->log('POST ' . $endpoint . ' ERR', $error);
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            if ($code >= 400) {
                $this->log('POST ' . $endpoint . ' HTTP', 'HTTP ' . $code);
                // možeš ovdje baciti exception ako želiš strogoću
                // throw new \RuntimeException("HTTP $code: $response");
            }

            return $this->resolveResponse($response);

        } catch (\Exception $exception) {
            $this->log('POST ' . $endpoint . ' EX', $exception->getMessage());
            return false;
        }
    }

    /** ===== Helpers ===== */

    private function resolveResponse($response)
    {
        // Ako nije JSON, vrati original (ili false)
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $response; // ponekad API vraća plain text
        }

        // Ako API pakira pod response.result → izvadi
        if (isset($decoded['response']['result'])) {
            return $decoded['response']['result'];
        }
        return $decoded;
    }

    private function resolveApiPassword(): string
    {
        return $this->username . ':' . $this->token . '_' . $this->password;
    }

    /**
     * Dodat sam 'json' granu; uvijek dodajemo Accept: application/json.
     */
    private function resolveHeaders(string $type, array $extra = []): array
    {
        $headers = [
            'Accept: application/json',
        ];

        if ($type === 'json') {
            $headers[] = 'Content-Type: application/json; charset=utf-8';
        } elseif ($type === 'form') {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        } elseif ($type === 'xml') {
            $headers[] = 'Content-Type: application/xml; charset=utf-8';
        }

        // Dodaj custom header-e (ako dođu kao ['X-Foo' => 'bar'] ili kao lista)
        foreach ($extra as $k => $v) {
            if (is_int($k)) {
                // već formirano "Header: value"
                $headers[] = $v;
            } else {
                $headers[] = $k . ': ' . $v;
            }
        }

        return $headers;
    }

    private function log(string $type, string $message, \Exception $exception = null): void
    {
        $log_name = 'eracuni_' . preg_replace('/[^a-z0-9_\- ]/i', '', $type);
        Log::store($message, $log_name);
        if ($exception) {
            Log::store($exception->getMessage(), $log_name);
        }
    }
}
