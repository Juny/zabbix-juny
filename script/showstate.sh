#!/bin/bash
# showrss.sh
# ps axo pid,rss,args|grep $1|grep $2|grep -v grep|grep -v bash|awk '{print$2}'
# echo 1234561
processor=`cat /proc/cpuinfo |grep processor|wc -l`
jstat_path=/usr/local/jdk1.7.0/bin/jstat
pid=`ps axo pid,cmd|grep $3|grep -v grep|grep -v top|grep -v showstate|awk '{print $1}'|head -n 1`
case "$1" in 
  "rss") ps axo pid,rss,cmd|grep $3|grep -v grep|grep -v bash|grep -v top|awk '{print$2}';;
  "thread") ps axo pid,nlwp,cmd|grep $3|grep -v grep|grep -v bash|awk '{print$2}';;
  "cpu") 
      cpu=`top -d 1 -bn 1 -p $pid |grep $pid|awk '{print $9}'`
      echo $cpu;; # `expr ${cpu%.*} / $processor`;;
  #"cpu") top -d 1 -bn 1 -c|grep $3|grep -v grep|awk '{print $9}';;
  "S0C") $jstat_path -gc $3 | grep -v S0C|awk '{print $1}';;
  "S1C") $jstat_path -gc $3 | grep -v S0C|awk '{print $2}';;
  "S0U") $jstat_path -gc $3 | grep -v S0C|awk '{print $3}';;
  "S1U") $jstat_path -gc $3 | grep -v S0C|awk '{print $4}';;
  "EC") $jstat_path -gc $3 | grep -v S0C|awk '{print $5}';;
  "EU") $jstat_path -gc $3 | grep -v S0C|awk '{print $6}';;
  "OC") $jstat_path -gc $3 | grep -v S0C|awk '{print $7}';;
  "OU") $jstat_path -gc $3 | grep -v S0C|awk '{print $8}';;
  "PC") $jstat_path -gc $3 | grep -v S0C|awk '{print $9}';;
  "PU") $jstat_path -gc $3 | grep -v S0C|awk '{print $10}';;
  "YGC") $jstat_path -gc $3 | grep -v S0C|awk '{print $11}';;
  "YGCT") $jstat_path -gc $3 | grep -v S0C|awk '{print $12}';;
  "FGC") $jstat_path -gc $3 | grep -v S0C|awk '{print $13}';;
  "FGCT") $jstat_path -gc $3 | grep -v S0C|awk '{print $14}';;
  "GCT") $jstat_path -gc $3 | grep -v S0C|awk '{print $15}';
esac
