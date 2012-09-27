<?php

namespace Igorw\Silex;

use Symfony\Component\Yaml\Yaml;

class Config {
    private $env;
    private $config       = null;
    private $replacements = array();

    public function __construct(Env $env, array $replacements = array()) {
        $this->env          = $env;
        $this->replacements = $replacements;
    }

    public function getConfig($key = null) {
        if (is_null($this->config)) {
            $this->buildConfig();
        }

        if (!is_null($key)) {
            return $this->getConfigByKey($key);
        } else {
            return $this->config;
        }
    }

    /**
     * retrieve config value by key
     *  since we mimic / mirror the behavory of Silex\Application we throw on miss
     *
     * @param  string    $key
     * @return mixed
     */
    public function getConfigByKey($key) {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        if (strstr($key, ".")) {
            $split = explode(".", $key);

            $config = $this->config;
            foreach ($split as $k) {
                $ok = false;
                if (array_key_exists($k, $config)) {
                    $config = $config[$k];
                    $ok = true;
                }
            }

            if ($ok) {
                return $config;
            }
        }

        throw new \Exception("Failed to retrieve config value for [{$key}]");

        return null;
    }

    private function buildConfig() {
        $config       = array();
        $replacements = array();

        foreach ($this->env->getEnvs() as $env) {
            $config = self::array_3d_merge($config, (array)self::readConfig("{$this->env->getCnfDir()}/{$env}.json"));
        }

        foreach ($this->replacements as $key => $value) {
            $replacements['%'.$key.'%'] = $value;
        }

        foreach ($config as $k => $v) {
            $config[$k] = $this->doReplacements($v, $replacements);
        }

        $this->config = $config;
    }

    private function doReplacements($value, array $replacements = array()) {
        if (!$replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v, $replacements);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $replacements);
        }

        return $value;
    }

    public static function readConfig($filename) {
        $format = self::getFileFormat($filename);

        if (!$filename || !$format) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }

        if (!file_exists($filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $filename));
        }

        if ('yaml' === $format) {
            if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
            }
            return Yaml::parse($filename);
        }

        if ('json' === $format) {
            $s = file_get_contents($filename);

            // strip posible /**/ comments
            $s = preg_replace('!/\*.*?\*/!s', '', $s);

            // strip `cnf = {};` wrapper (to satisfy editors
            $s = preg_replace('!^cnf = \{!', '{', $s);
            $s = preg_replace('!\};$!', '}', $s);

            if (($r = json_decode($s, true)) === null) {
                throw new \RuntimeException("Parsing JSON resulted in NULL [{$filename}]");
            }

            return $r;
        }

        throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears has invalid format '%s'.", $filename, $format));
    }

    protected static function getFileFormat($filename) {
        if (preg_match('#.ya?ml(.dist)?$#i', $filename)) {
            return 'yaml';
        }

        if (preg_match('#.json(.dist)?$#i', $filename)) {
            return 'json';
        }

        return pathinfo($filename, PATHINFO_EXTENSION);
    }


    protected function array_3d_merge($a, $b) {
        foreach ($b as $k => $v) {
            if (!array_key_exists($k, $a)) {
                $a[$k] = $v;
            } else if (is_array($v)) {
                if (!is_array($a[$k])) {
                    throw new \Exception("Trying to merge array into non array");
                }

                $a[$k] = self::array_3d_merge($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }
}
