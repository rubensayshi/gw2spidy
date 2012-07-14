<?php

namespace GW2Spidy\Util;

class CurlRequest {
    protected $url;
    protected $options;
    protected $headers;
    protected $urlAsReferer = true;
    protected $verbose      = false;
    protected $cookiejar;

    protected $result;

    protected static $defaultOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FAILONERROR    => false,
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
    }

    public static function newInstance($url, $options = array(), $headers = array()) {
        return new self($url, $options, $headers);
    }

    public function getResult() {
        if (is_null($this->result)) {
            $this->exec();
        }

        return $this->result;
    }

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
        $options[CURLOPT_COOKIEJAR]  = CookieJar::getInstance()->getCookieJar();
        $options[CURLOPT_COOKIEFILE] = CookieJar::getInstance()->getCookieJar();

        curl_setopt_array($ch, $options);

        $headers = $this->headers;

        $this->result = curl_exec($ch);
        curl_close($ch);

        if (!$this->result) {
            throw new Exception("CurlRequest failed");
        }

        return $this;
    }

    public function setOptions($options, $overwrite = false) {
        if ($overwrite) {
            $this->options = $options;
        } else {
            $this->options = $options + $this->options;
        }

        return $this;
    }

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
        $this->options[] = $header;

        return $this;
    }

    public function setVerbose($verbose = true) {
        $this->verbose = $verbose;

        return $this;
    }

}

?>