<?php

namespace GW2Spidy;

use \Exception;

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;

abstract class BaseSpider {
    protected static $instance;
    protected $cache;
    protected $loggedIn;

    public function __construct() {
        $this->cache = CacheHandler::getInstance('Spider');
    }

    public function __destruct() {
        $this->doLogout();
    }

    abstract protected function getLoginToUrl();

    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function ensureLogin() {
        if (!$this->loggedIn) {
            $this->loggedIn = $this->doLogin();
        }
    }

    public function doLogin() {
        $curl = CurlRequest::newInstance(AUTH_URL . "/login")
                    ->setOption(CURLOPT_POST, true)
                    ->setOption(CURLOPT_POSTFIELDS, http_build_query(array('email' => LOGIN_EMAIL, 'password' => LOGIN_PASSWORD)))
                    ->exec()
                    ;

        if ($sid = $curl->getResponseCookies('s')) {
            $loginURL = $this->getLoginToUrl() . "/authenticate";
            $loginURL .= "?account_name=". urlencode("Guild Wars 2");
            $loginURL .= "&session_key={$sid}";

            $curl = CurlRequest::newInstance($loginURL)
                        ->exec();
        } else {
            throw new Exception("Login request failed, no SID.");
        }

        if($curl->getInfo('http_code') >= 400) {
            throw new Exception("Login request failed with HTTP code {$curl->getInfo('http_code')}!");
        }

        return true;
    }

    public function doLogout() {
        try {
            $curl = CurlRequest::newInstance(AUTH_URL . "/logout")
                        ->exec()
                        ;
        } catch (Exception $e) {
            // no1 cares
        }
    }
}

?>
