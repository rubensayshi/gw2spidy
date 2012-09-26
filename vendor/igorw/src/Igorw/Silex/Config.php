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

    public function getConfig() {
        if (is_null($this->config)) {
            $this->buildConfig();
        }

        return $this->config;
    }

    private function buildConfig() {
        $config       = array();
        $replacements = array();

        foreach ($this->env->getEnvs() as $env) {
            $config += (array)$this->readConfig("{$this->env->getCnfDir()}/{$env}.json");
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

    private function readConfig($filename) {
        $format = $this->getFileFormat($filename);

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

            // strip posible js-style comments
            $s = preg_replace('!/\*.*?\*/!s', '', $s);
            $s = preg_replace('!^//!s', '', $s);
            $s = preg_replace('!^#!s', '', $s);
            $s = preg_replace('/\n\s*\n/', "\n", $s);

            if (($r = json_decode($s, true)) === null) {
                throw new \RuntimeException("Parsing JSON resulted in NULL [{$filename}]");
            }

            return $r;
        }

        throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears has invalid format '%s'.", $filename, $format));
    }

    public function getFileFormat($filename) {
        if (preg_match('#.ya?ml(.dist)?$#i', $filename)) {
            return 'yaml';
        }

        if (preg_match('#.json(.dist)?$#i', $filename)) {
            return 'json';
        }

        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}
