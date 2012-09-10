<?php

namespace GW2Spidy;

use GW2Spidy\Util\Singleton;

use \Exception;

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;

abstract class BaseSpider extends Singleton {
    protected $loggedIn;

    abstract protected function getLoginToUrl();

    public function ensureLogin() {
        if (!$this->loggedIn) {
            $this->loggedIn = $this->doLogin();
        }
    }

    public function doLogin() {
        if ($sid = GW2LoginManager::getInstance()->getSessionID()) {
            $loginURL = $this->getLoginToUrl() . "/authenticate";
            $loginURL .= "?account_name=". urlencode("Guild Wars 2");
            $loginURL .= "&session_key={$sid}";

            $curl = CurlRequest::newInstance($loginURL)
                        ->exec();
        } else {
            throw new Exception("Login request failed, no SID.");
        }

        return true;
    }
}

?>
