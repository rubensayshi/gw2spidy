#!/bin/bash

NUMBER=$1

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z "${NUMBER}" ]]; then
    echo "[[ EXIT ]] NO DAEMON NUMBER SPECIFIED \n"
    exit 1  
fi

while [[ true ]]; do 
    echo "restart"; 
    php ${ROOT}/daemons/worker-queue.php &>> /var/log/gw2spidy/worker-queue.${NUMBER}.log; 
done