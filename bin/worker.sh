#!/bin/bash

NUMBER=$1
PID_DIR=/var/

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z "${NUMBER}" ]]; then
    echo "[[ EXIT ]] NO DAEMON NUMBER SPECIFIED \n"
    exit 1
fi

if [ -n "$2" ]; then
    echo "done"
    exit 0
fi

while [[ true ]]; do 
    if [[ ! -e /var/run/gw2spidy/worker-${NUMBER}.pid ]]; then
        echo "pid file is gone, exit daemon [${NUMBER}] [$$]"
        exit 0
    fi
    
    if [[ -e /var/run/gw2spidy/worker-${i}.pid ]]; then
        if [[ ! `cat /var/run/gw2spidy/worker-${i}.pid` == $$ ]]; then
	        echo "pid file is cheating on us, has another PID, exit daemon [${NUMBER}] [$$]"
	        exit 0
	    fi
    fi

    echo "restart"; 
    php ${ROOT}/daemons/worker-queue.php &>> /var/log/gw2spidy/worker-queue.${NUMBER}.log; 
done