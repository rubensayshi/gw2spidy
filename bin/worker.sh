#!/bin/bash

NAME=$1
NUMBER=$2
PIDFILE=$3

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z "${NAME}" ]]; then
    echo "[[ EXIT ]] NO DAEMON NAME SPECIFIED \n"
    exit 1
fi
if [[ -z "${NUMBER}" ]]; then
    echo "[[ EXIT ]] NO DAEMON NUMBER SPECIFIED \n"
    exit 1
fi
if [[ -z "${PIDFILE}" ]]; then
    echo "[[ EXIT ]] NO DAEMON PIDFILE SPECIFIED \n"
    exit 1
fi

while [[ true ]]; do 
    if [[ ! -e $PIDFILE ]]; then
        echo "pid file is gone, exit daemon [${NUMBER}] [$$]"
        exit 0
    fi
    
    if [[ -e $PIDFILE ]]; then
        if [[ ! `cat ${PIDFILE}` == $$ ]]; then
	        echo "pid file is cheating on us, has another PID, exit daemon [${NUMBER}] [$$]"
	        exit 0
	    fi
    fi

    echo "restart"; 
    php ${ROOT}/daemons/${NAME}.php &>> /var/log/gw2spidy/${NAME}.${NUMBER}.log; 
done