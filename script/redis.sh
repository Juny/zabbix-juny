#!/bin/bash
case "$1" in 
  "alive") ps -ef | grep redis-server|grep `cat /var/run/redis$2.pid`|wc -l;;
  "query") redis-cli -p $2 info|grep $3":"|awk -F ':' '{print $2}';; 
  "querydb") redis-cli -p $2 info|grep "db[0-9]*:"|awk -F ':' '{print $2}';; 
  #"keys") ps axo pid,nlwp,args|grep $3|grep -v grep|grep -v bash|awk '{print$2}';;
esac
