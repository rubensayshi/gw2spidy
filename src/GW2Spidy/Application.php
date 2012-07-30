<?php

namespace GW2Spidy;

class Application {
    protected $basedir;
    protected $time;

    protected static $instance;

    protected function __construct() {
        $this->basedir = dirname(dirname(dirname(__FILE__)));
        $this->time    = microtime(true);
    }

    /**
     * @return \GW2Spidy\Application
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function render($tpl, array $args = array()) {
        ob_start();

        extract($args);

        $tplpath = "{$this->basedir}/templates/{$tpl}";

        if (!preg_match("/\.php$/", $tplpath)) {
            $tplpath .= ".php";
        }

        if (!file_exists($tplpath)) {
            throw new \Exception("Failed to find template [{$tplpath}]");
        }

        require $tplpath;

        return ob_get_clean();
    }

    public function isCLI() {
        return (php_sapi_name() == 'cli');
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