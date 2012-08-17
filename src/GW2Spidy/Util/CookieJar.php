<?php

namespace GW2Spidy\Util;

class CookieJar {
    protected $cookiejar;

    protected static $instance;

    public function __destruct() {
        $this->cleanupCookieJar();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
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
    }
}

?>