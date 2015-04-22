#!/usr/bin/env python
# -*- coding: utf-8 -*-
#args1:port
#args2:db index

import sys, os, re
dbrows = os.popen("redis-cli -p " + sys.argv[1] + " info | grep db" + sys.argv[2]).readlines()
v = re.match(".*keys=([\d].*),.*",dbrows[0]) 
print v.groups()[0]

