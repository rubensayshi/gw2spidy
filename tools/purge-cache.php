<?php

use GW2Spidy\Dataset\DatasetManager;

use GW2Spidy\Util\CacheHandler;


require dirname(__FILE__) . '/../autoload.php';

// memcache is completely purged this way
CacheHandler::getInstance("purge")->purge();

DatasetManager::getInstance()->purgeCache();

if (function_exists('apc_clear_cache')) {
    $hash = md5(uniqid());
    $targetDir = dirname(dirname(__FILE__)) . "/webroot/tmp";
    $target = "{$targetDir}/{$hash}.php";

    if (!is_dir($targetDir)) {
        mkdir($targetDir);
    }

    copy(dirname(__FILE__) . "/clear_apc_cache.php", $target);

    $url = "http://localhost/tmp/{$hash}.php";
    $result = json_decode(file_get_contents($url), true);

    if (isset($result['success']) && $result['success']) {
        echo "cleared apc \n";
    } else {
        echo "failed to clear apc \n";
    }

    unlink($target);
}

echo "done \n";