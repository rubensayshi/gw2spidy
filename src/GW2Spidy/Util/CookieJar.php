<?php

namespace GW2Spidy\Util;

class CookieJar extends Singleton {
    protected $cookiejar;

    public function __destruct() {
        $this->cleanupCookieJar();
    }

    public function getCookieJar() {
        if (is_null($this->cookiejar)) {
            $this->cookiejar = "/tmp/" . uniqid("cookie_jar");
            touch($this->cookiejar);
        }

        return $this->cookiejar;
    }

    public function cleanupCookieJar() {
        unlink($this->getCookieJar());
        $this->cookiejar = null;
    }
}

?>