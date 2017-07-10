#!/bin/bash

LOGDIR="/var/log/gw2spidy"

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

echo "Start $SECONDS"
php ${ROOT}/tools/update-items-from-api.php

echo "Items created $SECONDS"
php ${ROOT}/tools/create-recipe-map.php recipemap

echo "recipemap created $SECONDS"
php ${ROOT}/tools/import-recipe-map.php recipemap

rm recipemap
echo "Finish $SECONDS"
