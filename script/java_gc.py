#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import re
import sys

result = '{\n\t"data":[\n\t\t'
jps = os.popen("/usr/local/jdk1.7.0/bin/jps|grep -v Jps|awk '{print $1}'").readlines()
#jps = os.popen("ls -l /tmp/hsperfdata_root/|grep -v total|awk '{print $9}'").readlines()
for pid in jps:
        result = result + '{"{#APPNAME}":"Java","{#APPPID}":"' +  pid[0:-1] + '"},'
        #rows = os.popen("jstat -gc " + pid[0:-1]).readlines()
        #gc_column=re.match("\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)",rows[0])
        #gc_value=re.match("\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)",rows[1])
        #for i in range(0,len(gc_column.groups())):     
        #        result = result + '"{#' +  gc_column.groups()[i] + '}":"' +  gc_value.groups()[i] + '",'
        #result = result[0:-1] + "},\n\t\t"
result = result[0:-1] + "\n\t]\n}"
print result
