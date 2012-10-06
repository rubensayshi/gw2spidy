#!/bin/bash

LISTING_CNT=$1
ITEM_CNT=$2
GEM_CNT=$3
LOGDIR="/var/log/gw2spidy"

if [ -z "$ROOT" ]; then
    ROOT=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`
    export ROOT
fi

if [[ -z "${LISTING_CNT}" ]]; then
    LISTING_CNT=1
fi
if [[ -z "${ITEM_CNT}" ]]; then
    ITEM_CNT=1
fi
if [[ -z "${GEM_CNT}" ]]; then
    GEM_CNT=1
fi

if [ ! -d "${LOGDIR}" ]; then 
    mkdir -p ${LOGDIR}
fi

if [ -d "${LOGDIR}/archive" ]; then
      rm -rf ${LOGDIR}/archive
fi

mkdir ${LOGDIR}/archive
mv ${LOGDIR}/*.log ${LOGDIR}/archive
rm -f ${LOGDIR}/*.log

function start_worker {
	NAME=$1
	NUM=$2
	PIDFILE="/var/run/gw2spidy/${NAME}-${NUM}.pid"
    if [[ -e $PIDFILE ]]; then
        PID=$(cat $PIDFILE)
                        
        if [ -e /proc/$PID -a /proc/$PID/exe ]; then
            echo "already running daemon ${NAME} number ${NUM}; [[ ${PID} ]]"
            continue
        fi
    fi
    
    echo "startin daemon ${NAME} number ${NUM}"
    
    ((${ROOT}/bin/worker.sh $NAME $NUM $PIDFILE &>> ${LOGDIR}/start-workers.log) & echo $! > $PIDFILE &)
}

for ((i = 0; i < LISTING_CNT; i++)); do 
	start_worker "item-listing-worker" $i
done

for ((i = 0; i < ITEM_CNT; i++)); do 
	start_worker "item-worker" $i
done

for ((i = 0; i < GEM_CNT; i++)); do 
	start_worker "gem-worker" $i
done


