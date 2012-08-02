<?php

namespace GW2Spidy;

class Application extends \Silex\Application {
    protected $time;

    public function __construct() {
        $this->time    = microtime(true);

        parent::__construct();
    }

    public function debugSQL() {
        $con = \Propel::getConnection();

        $con->setLogLevel(\Propel::LOG_DEBUG);
        $con->useDebug(true);
    }

    public function getTime() {
        return microtime(true) - $this->time;
    }
}

?>