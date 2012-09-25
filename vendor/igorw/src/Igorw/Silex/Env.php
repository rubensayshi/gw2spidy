<?php

namespace Igorw\Silex;

class Env {
    protected $envfile = null;
    protected $envs    = null;

    public function __construct($envfile = null) {
        $this->envfile = $envfile;
    }

    public function getEnvs() {
        if (is_null($this->envs)) {
            $this->envs = array();

            if ($this->envfile) {
                if ($envs = $this->atemptRetrieveFromCache()) {
                    if (!in_array('dev', $envs)) {
                        $this->envs = $envs;
                    }
                }

                if (!$this->envs && file_exists($this->envfile)) {
                    if (!is_readable($this->envfile)) {
                        throw new Exception("Env file is there but not readable.");
                    }

                    if ($envs = file_get_contents($this->envfile)) {
                        if ($envs = array_filter(array_map('trim', explode("\n", $envs)))) {
                            $this->envs = $envs;

                            $this->atemptStoreInCache($this->envs);
                        }
                    }
                }
            }
        }

        return $this->envs;
    }

    public function atemptRetrieveFromCache() {
        if (function_exists('apc_fetch')) {
            return apc_fetch($this->envfile);
        }

        return null;
    }

    public function atemptStoreInCache($envs) {
        if (function_exists('apc_store')) {
            return apc_store($this->envfile, $envs);
        }

        return null;
    }
}
