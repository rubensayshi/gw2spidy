<?php

namespace Igorw\Silex;

class Env {
    protected $cnfdir = null;
    protected $env    = null;
    protected $envs   = null;

    public function __construct($cnfdir = null) {
        $this->cnfdir  = $cnfdir;
    }

    public function getCnfDir() {
        return $this->cnfdir;
    }

    public function getEnvFile() {
        return "{$this->getCnfDir()}/env";
    }

    public function getEnv() {
        return end($this->getEnvs());
    }

    public function getEnvs() {
        if (is_null($this->envs)) {
            $this->envs = array();

            if ($this->getEnvFile()) {
                if ($envs = $this->atemptRetrieveFromCache()) {
                    if (!in_array('dev', $envs)) {
                        $this->envs = $envs;
                    }
                }

                if (!$this->envs && file_exists($this->getEnvFile())) {
                    if (!is_readable($this->getEnvFile())) {
                        throw new \Exception("Env file is there but not readable.");
                    }

                    if ($envs = file_get_contents($this->getEnvFile())) {
                        if ($envs = array_reverse(array_filter(array_map('trim', explode("\n", $envs))))) {
                            $this->envs = $envs;

                            $this->atemptStoreInCache($this->envs);
                        }
                    }
                } else {
                    throw new \Exception("No `env` file found, read the README on how to setup the (new style) config!");
                }
            }
        }

        return $this->envs;
    }

    public function atemptRetrieveFromCache() {
        if (function_exists('apc_fetch')) {
            return apc_fetch($this->getEnvFile());
        }

        return null;
    }

    public function atemptStoreInCache($envs) {
        if (function_exists('apc_store')) {
            return apc_store($this->getEnvFile(), $envs);
        }

        return null;
    }
}
