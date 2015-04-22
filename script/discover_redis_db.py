#!/usr/bin/env python
# -*- coding: utf-8 -*-

import MySQLdb, socket, sys, re, os    
user = 'admin'
passwd = 'admin'
dbhost = '192.168.110.216'
dbname = 'FAEDB'
args = ''

def get_local_ip(ifname = 'eth0'):  
    import socket, fcntl, struct  
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)  
    inet = fcntl.ioctl(s.fileno(), 0x8915, struct.pack('256s', ifname[:15]))  
    ret = socket.inet_ntoa(inet[20:24])  
    return ret  

def main():
        if len(sys.argv)> 1:
                args = sys.argv[1]
        file = open("/etc/ha/env.conf")
        txt = file.read()
        m = re.match('[\s\S]*HA_SERVER_ADDRESS.*?(\d.*?)",[\s\S]',txt)
        myaddr = m.groups()[0] #get_local_ip() #socket.gethostbyname(socket.getfqdn(socket.gethostname()))
        file.close()
        try:     
            conn = MySQLdb.connect(host=dbhost, user=user, passwd=passwd, connect_timeout=2, db=dbname)     
            cursor = conn.cursor()     
            sql = "SELECT RoleName,NodeOrder,RouteValue FROM `FAE_RedisCluster`"
            cursor.execute(sql)     
            alldata = cursor.fetchall()  
            result = '{\n\t"data":[\n'
            for data in alldata:   
                    m = re.match('.*?host=(\d.*)\n.*?port=(\d.*)\n.*?database=(\d.*)\n',data[2])
                    ip = m.groups()[0]
                    port = m.groups()[1]
                    database = m.groups()[2]
                    if ip == myaddr :  
                        result = result + '\t\t{"{#APPNAME}":"' + str(data[0]) + "_" + str(data[1]) + '","{#PORT}":"' + str(port) + '","{#DB}":"' + str(database) + '"},\n'
            cursor.close()     
            conn.close()     
            result = result[0:-2] + "\n\t]\n}"
            print result
        except Exception, e:       
            print e       
            sys.exit() 

if __name__ == '__main__':
        main()
