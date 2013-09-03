<?php

namespace GW2Spidy;

use GW2Spidy\Util\CookieJar;

use GW2Spidy\DB\GW2Session;

use GW2Spidy\DB\GW2SessionQuery;

use GW2Spidy\Util\Singleton;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GW2SessionManager extends Singleton {
    protected $gw2session;
    protected $cookieJar;

    public function getSessionKey() {
        return $this->getSession()->getSessionKey();
    }

    public function getCookieJar() {
        return $this->cookieJar;
    }

    public function getSession() {
        if (is_null($this->gw2session)) {
            $this->gw2session = $this->_getSession();
        }

        return $this->gw2session;
    }

    protected function _getSession() {
        return $this->getNewSession();
    }

    public function checkSessionAlive(GW2Session $gw2session) {
        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.tradingpost_url'))
                    ->setCookie("s={$gw2session->getSessionKey()}")
                    ->setThrowOnError(false)
                    ->exec()
                    ;

        if ($curl->getInfo('http_code') < 400) {
            return true;
        } else if ($curl->getInfo('http_code') == 503) { // detect expired
            return false;
        } else if ($curl->getInfo('http_code') == 401) { // detect failed
            return false;
        } else {
            throw new Exception("Tradingpost seems down!");
        }
    }

    protected function getNewSession() {
        $this->cookieJar = new CookieJar();
        $curl = CurlRequest::newInstance(getAppConfig('gw2spidy.auth_url') . "/login")
                    ->setCookieJar($this->cookieJar)
                    ->setOption(CURLOPT_POST, true)
                    ->setOption(CURLOPT_POSTFIELDS, http_build_query(array('email' => getAppConfig('gw2spidy.auth_email'), 'password' => getAppConfig('gw2spidy.auth_password'))))
                    ->exec()
                    ;

        if ($sid = $curl->getResponseCookies('s')) {
            $gw2session = new GW2Session();
            $gw2session->setSessionKey($sid);
            $gw2session->setSource("generated");
            $gw2session->setGameSession(false);

            var_dump("getNewSession", $sid);

            return $gw2session;
        } else {
            throw new Exception("Login request failed, no SID.");
        }
    }
}
