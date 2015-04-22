#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import re
import sys

i=3
while i>0:
    cmd = "./showstate.sh " + sys.argv[1] + " " + sys.argv[2] + " " + sys.argv[3]
    print cmd
    state = os.popen(cmd).readlines()
    if len(state) == 0:
        i = i - 1
    else:
        print "state:"
        print state
        break
