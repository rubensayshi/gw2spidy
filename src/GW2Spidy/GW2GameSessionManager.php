<?php

namespace GW2Spidy;

use GW2Spidy\DB\GW2Session;

use GW2Spidy\DB\GW2SessionQuery;

use GW2Spidy\Util\Singleton;

use GW2Spidy\Util\CurlRequest;
use \Exception;

class GW2GameSessionManager extends Singleton {
    protected $gw2session;

    public function getSessionKey() {
        return $this->getSession()->getSessionKey();
    }


    public function getSession() {
        if (is_null($this->gw2session)) {
            $this->gw2session = $this->_getSession();
        }

        return $this->gw2session;
    }

    protected function _getSession() {
        $q = GW2SessionQuery::create()
                            ->filterByGameSession(true)
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
            throw new Exception("We're currently unable to generate a new game session on demand :-(.");
    }
}

?>
