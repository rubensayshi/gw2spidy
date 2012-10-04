#!/bin/bash

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z `ls /var/run/gw2spidy | grep "worker-.*.pid"` ]]; then
    echo "no worker pid files"  
    exit 0
fi

if [ "$1" == "now" ]; then
    PIDS=$(cat /var/run/gw2spidy/*.pid)

    rm -f /var/run/gw2spidy/*.pid

    for PID in $PIDS; do        
        if [ -e /proc/$PID -a /proc/$PID/exe ]; then
            echo "stopping daemon [[ ${PID} ]]"
            CPIDS=$(pgrep -P ${PID})
            kill ${PID}
            
            for CPID in $CPIDS; do
                echo "stopping daemon spawn [[ ${CPID} ]]"
                kill $CPID
            done
        fi        
    done
else
    echo "removing pid files so workers will kill themselves"
    rm -f /var/run/gw2spidy/*.pid
fi