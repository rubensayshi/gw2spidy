<?php

namespace GW2Spidy;

use GW2Spidy\DB\GW2Session;

use GW2Spidy\DB\GW2SessionQuery;

use GW2Spidy\Util\Singleton;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GW2SessionManager extends Singleton {
    protected $gw2session;

    public function getSession() {
        if (is_null($this->gw2session)) {
            $this->gw2session = $this->_getSession();
        }

        return $this->gw2session;
    }

    protected function _getSession() {
        $q = GW2SessionQuery::create()
                            ->orderByGameSession('DESC')
                            ->orderByCreated('DESC');

        while ($gw2session = $q->findOne()) {
            if (!$this->checkSessionAlive($gw2session)) {
                $gw2session->delete();
            } else {
                return $gw2session;
            }
        }

        return $this->getNewSession();
    }

    protected function checkSessionAlive(GW2Session $gw2session) {
        $curl = CurlRequest::newInstance(TRADINGPOST_URL)
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
        $curl = CurlRequest::newInstance(AUTH_URL . "/login")
                    ->setOption(CURLOPT_POST, true)
                    ->setOption(CURLOPT_POSTFIELDS, http_build_query(array('email' => LOGIN_EMAIL, 'password' => LOGIN_PASSWORD)))
                    ->exec()
                    ;

        if ($sid = $curl->getResponseCookies('s')) {
            $gw2session = new GW2Session();
            $gw2session->setSessionKey($sid);
            $gw2session->setSource("generated");
            $gw2session->setGameSession(false);

            $gw2session->save();

            return $gw2session;
        } else {
            throw new Exception("Login request failed, no SID.");
        }
    }
}

?>
