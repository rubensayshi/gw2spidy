<?php

use GW2Spidy\Dataset\DatasetManager;

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

// memcache is completely purged this way
if (array_intersect(array('-m', '--all'), $argv)) {
    CacheHandler::getInstance("purge")->purge();
    echo "cleared memcache \n";
}

if (array_intersect(array('-d', '--all'), $argv)) {
    DatasetManager::getInstance()->purgeCache();
    echo "cleared dataset \n";
}

if (array_intersect(array('-a', '--all'), $argv) && function_exists('apc_clear_cache') && ($host = getAppConfig("apc_clear_cache_host"))) {
    apc_clear_cache();
    apc_clear_cache('user');
    apc_clear_cache('opcode');

    $hash = md5(uniqid());
    $targetDir = dirname(dirname(__FILE__)) . "/webroot/tmp";
    $target = "{$targetDir}/{$hash}.php";

    if (!is_dir($targetDir)) {
        mkdir($targetDir);
    }

    copy(dirname(__FILE__) . "/clear_apc_cache.php", $target);

    $url = "http://{$host}/tmp/{$hash}.php";
    $result = json_decode(file_get_contents($url), true);

    if (isset($result['success']) && $result['success']) {
        echo "cleared apc \n";
    } else {
        echo "failed to clear apc \n";
    }

    unlink($target);
}

echo "done \n";