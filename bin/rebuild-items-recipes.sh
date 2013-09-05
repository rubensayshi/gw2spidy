#!/bin/bash

LOGDIR="/var/log/gw2spidy"

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

php ${ROOT}/tools/update-items-from-api.php
php ${ROOT}/tools/create-recipe-map.php recipemap
php ${ROOT}/tools/import-recipe-map.php recipemap
rm recipemap