<?php

namespace GW2Spidy\Util;

abstract class Singleton {
    protected static $instances;

    /**
     * @return static
     */
    public static function getInstance() {
        $class = get_called_class();

        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new $class();
        }

        return static::$instances[$class];
    }
}

?>
