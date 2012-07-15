#!/bin/bash

PROJECTDIR="/var/sandbox/gw2spidy"

cd $PROJECTDIR/config
$PROJECTDIR/propel-gen 
cp $PROJECTDIR/config/build/classes/gw2spidy/map/* $PROJECTDIR/src/GW2Spidy/DB/map/
cp $PROJECTDIR/config/build/classes/gw2spidy/om/*  $PROJECTDIR/src/GW2Spidy/DB/om/
cp $PROJECTDIR/config/build/conf/* $PROJECTDIR/config/
cp $PROJECTDIR/config/build/sql/schema.sql $PROJECTDIR/config/
rm -rf $PROJECTDIR/config/build/ 
