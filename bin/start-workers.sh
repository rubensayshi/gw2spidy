#!/bin/bash

CNT=$1

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z "${CNT}" ]]; then
    CNT=1
fi

for ((i = 0; i < CNT; i++)); do 
    nohup ${ROOT}/bin/worker.sh $i  &> /var/log/gw2spidy/start-workers.log &
done