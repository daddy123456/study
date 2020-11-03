<?php

declare(strict_types=1);

namespace Src\Curl;


class Curl
{
    /** @var false|resource  */
    private $ch;

    public function __construct(string $url, int $responseHeader = 0)
    {
        $this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, $responseHeader);
    }

    /** send request*/
    private function exec()
    {
        return curl_exec($this->ch);
    }

    /** get request */
    public function get()
    {
        return $this->exec();
    }

    /** post request */
    public function post($value, bool $https = true)
    {
        if ($https) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($value));
        return $this->exec();
    }

    /** @param  array $value */
    public function addHeader(array $value)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $value);
        return $this;
    }

    private function close()
    {
        curl_close($this->ch);
    }

    public function __destruct()
    {
        $this->close();
    }
}
