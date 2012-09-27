<?php

use GW2Spidy\Util\CLIColors;

use GW2Spidy\Util\Functions;

use Igorw\Silex\Config;

require dirname(__FILE__) . '/../autoload.php';

$name  = isset($argv[1]) ? $argv[1] : null;
$check = isset($argv[2]) ? $argv[2] : null;
$env   = isset($argv[3]) ? $argv[3] : null;

// '-' is null too ;) just so you can skip the arg if you want
$check  = $check == '-' ? null : $check;
// grab default env
$env  = $env ?: getAppEnv()->getEnv();
// generate md5
$md5  = md5($name);
// cnf file based on env
$cnffile = __DIR__ . "/../config/cnf/{$env}.json";

echo CLIColors::getColoredString("adding md5 [[ {$md5} ]] for [[ {$name} ]] to [[ {$env} ]] ... \n", "light_blue");

if (in_array($env, array('dev', 'prod', 'default'))) {
    die(CLIColors::getColoredString("Can't add API keys to [[ {$env} ]] cnf file, protected.\n", "red"));
}


if (!file_exists($cnffile)) {
    die(CLIColors::getColoredString("Config file [[ {$cnffile} ]] doesn't exist.\n", "red"));
}

$cnf = Config::readConfig($cnffile);

if (!$cnf) {
    die(CLIColors::getColoredString("Parsing cnf failed.\n", "red"));
}

if (!isset($cnf['gw2spidy']['api_secrets'])) {
    die(CLIColors::getColoredString("Please create the initial 'api_secrets' entry yourself.\n", "red"));
}

$md5 = md5($name);

if ($check && $check != $md5) {
    die(CLIColors::getColoredString("md5 [[ {$md5} ]] didn't match the check [[ {$check} ]].\n", "red"));
}

$cnf['gw2spidy']['api_secrets'][$name] = $md5;

$ok = file_put_contents($cnffile, "cnf = ".Functions::indent(json_encode($cnf)).";");

if (!$ok) {
    die(CLIColors::getColoredString("Failed writing to file [[ {$cnffile} ]].\n", "red"));
}

echo CLIColors::getColoredString("Added md5 [[ {$md5} ]] for [[ {$name} ]]\n", "green");



