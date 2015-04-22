#!/bin/bash
#ps -ef | grep zabbix|grep -v grep|awk '{print $2}'|xargs kill
killall -9 zabbix_agentd
rm -rf /usr/local/etc/zabbix_agent*
cd /usr/local/etc
mkdir zabbix_agent.conf.d
mkdir zabbix_agentd.conf.d
wget http://192.168.245.2:8081/deploy/etc/zabbix_agent.conf -P .
wget http://192.168.245.2:8081/deploy/etc/zabbix_agentd.conf -P .
cd  zabbix_agentd.conf.d
wget http://192.168.245.2:8081/deploy/etc/zabbix_agentd.conf.d/userparameter_discover_faeapp.conf -P .
wget http://192.168.245.2:8081/deploy/etc/zabbix_agentd.conf.d/userparameter_GJY.conf -P .
cd /usr/local/zabbix
rm -rf ./*
wget http://192.168.245.2:8081/deploy/script/discover_faeapp.py -P .
wget http://192.168.245.2:8081/deploy/script/showstate.sh -P .
wget http://192.168.245.2:8081/deploy/script/update.sh -P .
wget http://192.168.245.2:8081/deploy/script/v1.sh -P .
wget http://192.168.245.2:8081/deploy/script/v2.sh -P .
wget http://192.168.245.2:8081/deploy/script/v3.sh -P .
wget http://192.168.245.2:8081/deploy/script/v4.sh -P .
wget http://192.168.245.2:8081/deploy/script/v5.sh -P .
chmod +x /usr/local/zabbix/*
zabbix_agentd

