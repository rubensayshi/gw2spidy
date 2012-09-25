<?php

namespace Igorw\Silex;

class Env {
    const ENV_UNKNOWN = 'unknown';

    protected $envfile = null;
    protected $env     = null;

    public function __construct($envfile = null) {
        $this->envfile = $envfile;
    }

    public function getEnv() {
        if (is_null($this->env)) {
            if ($this->envfile) {
                if ($env = $this->atemptRetrieveFromCache()) {
                    $this->env = $env;
                } else if (file_exists($this->envfile)) {
                    if (!is_readable($this->envfile)) {
                        throw new Exception("Env file is there but not readable.");
                    }

                    if ($env = trim(file_get_contents($this->envfile))) {
                        $this->env = $env;
                        $this->atemptStoreInCache($this->env);
                    }
                }
            }

            if (!$this->env) {
                 $this->env = self::ENV_UNKNOWN;
            }
        }

        return $this->env;
    }

    public function atemptRetrieveFromCache() {
        if (function_exists('apc_fetch')) {
            return apc_fetch($this->envfile);
        }

        return null;
    }

    public function atemptStoreInCache($env) {
        if (function_exists('apc_store')) {
            return apc_store($this->envfile, $env);
        }

        return null;
    }
}
