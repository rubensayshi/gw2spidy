#!/bin/bash

PROJECTDIR=`php -r "echo dirname(dirname(realpath('$(pwd)/$0')));"`

cd $PROJECTDIR/config
$PROJECTDIR/vendor/propel/generator/bin/propel-gen
cp $PROJECTDIR/config/build/classes/gw2spidy/map/* $PROJECTDIR/src/GW2Spidy/DB/map/
cp $PROJECTDIR/config/build/classes/gw2spidy/om/*  $PROJECTDIR/src/GW2Spidy/DB/om/
cp $PROJECTDIR/config/build/conf/* $PROJECTDIR/config/
cp $PROJECTDIR/config/build/sql/schema.sql $PROJECTDIR/config/
rm -rf $PROJECTDIR/config/build/ 
