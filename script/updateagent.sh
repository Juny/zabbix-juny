#!/bin/bash
for ip in `cat $1 | grep -v '#'`
do
  echo "begin update" $ip 
  ssh $ip "killall zabbix_agentd"
  ssh $ip "rm -rf /usr/local/zabbix/*"
  scp /usr/local/zabbix/* root@$ip:/usr/local/zabbix/
  ssh $ip "rm -rf /usr/local/etc/zabbix*"
  scp -r /usr/local/etc/zab* root@$ip:/usr/local/etc/
  sleep 2
  ssh $ip "zabbix_agentd"
  echo $ip "............................ is ok."
done
echo "update complete."