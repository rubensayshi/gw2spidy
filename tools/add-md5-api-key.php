<?php

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

echo "adding md5 [[ {$md5} ]] for [[ {$name} ]] to [[ {$env} ]] ... \n";

if (in_array($env, array('dev', 'prod', 'default'))) {
    die("Can't add API keys to [[ {$env} ]] cnf file, protected.\n");
}


if (!file_exists($cnffile)) {
    die("Config file [[ {$cnffile} ]] doesn't exist.\n");
}

$cnf = Config::readConfig($cnffile);

if (!$cnf) {
    die("Parsing cnf failed.\n");
}

if (!isset($cnf['gw2spidy']['api_secrets'])) {
    die("Please create the initial 'api_secrets' entry yourself.\n");
}

$md5 = md5($name);

if ($check && $check != $md5) {
    die("md5[[ {$md5} ]] didn't match the check[[ {$check} ]]\n");
}

$cnf['gw2spidy']['api_secrets'][$name] = $md5;

$ok = file_put_contents($cnffile, "cnf = ".Functions::indent(json_encode($cnf)).";");

if (!$ok) {
    die("Failed writing to file [[ {$cnffile} ]]\n");
}

echo "Added md5[[ {$md5} ]] for [[ {$name} ]]\n";



