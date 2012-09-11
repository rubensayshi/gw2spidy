<?php

namespace GW2Spidy\Util;

use \Exception;

class CurlRequest {
    protected $url;
    protected $options;
    protected $headers;
    protected $cookies;
    protected $urlAsReferer = true;
    protected $verbose      = false;
    protected $cookiejar;
    protected $throwOnError = true;

    protected $result;
    protected $info;
    protected $responseHeaders;
    protected $responseBody;
    protected $responseCookies;

    protected static $defaultOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FAILONERROR    => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HEADER         => true,
    );
    protected static $defaultHeaders = array(
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language: en-us,en;q=0.5",
        // while we can get raw, we don't have to gunzip the response <3
        "Accept-Encoding: deflate",
        "Connection: keep-alive",
        "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:13.0) Gecko/20100101 Firefox/13.0.1",
    );

    public function __construct($url, $options = array(), $headers = array()) {
        $this->url     = $url;
        $this->options = $options + self::$defaultOptions;
        $this->headers = $headers + self::$defaultHeaders;
        $this->cookies = array();
    }

    /**
     *
     * @return \GW2Spidy\Util\CurlRequest
     */
    public static function newInstance($url, $options = array(), $headers = array()) {
        return new self($url, $options, $headers);
    }

    public function getResult() {
        if (is_null($this->result)) {
            $this->exec();
        }

        return $this->result;
    }

    public function getInfo($key = null) {
        if (is_null($this->info)) {
            $this->exec();
        }

        if (is_null($key)) {
            return $this->info;
        } else {
            return isset($this->info[$key]) ? $this->info[$key] : null;
        }
    }

    public function getResponseHeaders($key = null) {
        if (is_null($this->responseHeaders)) {
            $this->exec();
        }


        if (is_null($key)) {
            return $this->responseHeaders;
        } else {
            return isset($this->responseHeaders[$key]) ? $this->responseHeaders[$key] : null;
        }
    }

    public function getResponseCookies($key = null) {
        if (is_null($this->responseCookies)) {
            $this->exec();
        }


        if (is_null($key)) {
            return $this->responseCookies;
        } else {
            return isset($this->responseCookies[$key]) ? $this->responseCookies[$key] : null;
        }
    }

    public function getResponseBody() {
        if (is_null($this->responseBody)) {
            $this->exec();
        }

        return $this->responseBody;
    }

    /**
     * @throws Exception
     * @return \GW2Spidy\Util\CurlRequest
     */
    public function exec() {
        if (!is_null($this->result)) {
            throw new Exception("Can't reuse CurlRequest");
        }

        $ch = curl_init($this->url);

        $options = $this->options;
        if ($this->verbose) {
            $options[CURLOPT_VERBOSE] = true;
        }
        if ($this->urlAsReferer) {
            $options[CURLOPT_REFERER] =  $this->url;
        }
        if ($this->cookies) {
            $options[CURLOPT_COOKIE] = implode("; ", $this->cookies);
        }
        $options[CURLOPT_COOKIEJAR]  = CookieJar::getInstance()->getCookieJar();
        $options[CURLOPT_COOKIEFILE] = CookieJar::getInstance()->getCookieJar();
        $options[CURLOPT_HTTPHEADER] = array_merge($this->headers, isset($options[CURLOPT_HTTPHEADER]) ? $options[CURLOPT_HTTPHEADER] : array());

        curl_setopt_array($ch, $options);

        $this->result = curl_exec($ch);
        $this->info   = curl_getinfo($ch);

        curl_close($ch);

        if ($this->throwOnError && $this->getInfo('http_code') >= 400) {
            throw new Exception("CurlRequest failed [[ {$this->getInfo('http_code')} ]] [[ {$this->url} ]]");
        }

        $this->responseHeaders = array();
        $this->responseCookies = array();

        // if we've requested headers we can parse them now
        if ($options[CURLOPT_HEADER]) {
            // retrieve header string based on reponse info
            $responseHeaders    = trim(substr($this->result, 0, $this->info['header_size']));
            // retrieve body string based on reponse info
            $this->responseBody = substr($this->result, $this->info['header_size']);

            $responseHeaders = explode("\r\n\r\n", $responseHeaders);

            foreach ($responseHeaders as $responseHeader) {
                $this->extractHeaders($responseHeader, true);
            }

            $this->extractHeaders(end($responseHeaders));
        } else {
            $this->responseBody = $this->result;
        }

        return $this;
    }

    protected function extractHeaders($responseHeader, $cookiesonly = false) {
        $responseHeader = str_replace("\r\n", "\n", $responseHeader);

        // explode and parse the headers
        foreach (explode("\n", $responseHeader) as $line) {
            $line = explode(':', $line, 2);

            if (count($line) != 2) {
                continue;
            }

            list($k, $v) = $line;

            // cookies \o/ nomnomnom
            if (strtolower(trim($k)) == 'set-cookie') {
                $cookiesplit = explode("=", $v);

                if (count($cookiesplit) < 2) {
                    continue;
                } else if (count($cookiesplit) > 2) {
                    // this is dirty as @&#%@ but let's roll with this for now
                    $cookiesplit = array($cookiesplit[0], reset(explode(" ", $cookiesplit[1])));
                }

                if (substr($cookiesplit[1], -1, 1) == ";") {
                    $cookiesplit[1] = substr($cookiesplit[1], 0, -1);
                }

                $this->responseCookies[trim($cookiesplit[0])] = trim($cookiesplit[1]);
            } else if (!$cookiesonly) {
                $this->responseHeaders[trim($k)] = trim($v);
            }
        }
    }

    public function setOptions($options, $overwrite = false) {
        if ($overwrite) {
            $this->options = $options;
        } else {
            $this->options = $options + $this->options;
        }

        return $this;
    }

    /**
     *
     * @return \GW2Spidy\Util\CurlRequest
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;

        return $this;
    }

    public function setHeaders($headers, $overwrite = false) {
        if ($overwrite) {
            $this->headers = $headers;
        } else {
            $this->headers = $headers + $this->headers;
        }

        return $this;
    }

    public function setHeader($header) {
        $this->headers[] = $header;

        return $this;
    }

    public function setCookie($cookie) {
        $this->cookies[] = $cookie;

        return $this;
    }

    public function setVerbose($verbose = true) {
        $this->verbose = $verbose;

        return $this;
    }

    public function setThrowOnError($throw = true) {
        $this->throwOnError = $throw;

        return $this;
    }
}

?>
