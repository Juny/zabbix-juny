#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys

result=os.popen("iostat -dx  1 2|grep " + sys.argv[1] + "|tail -n 1|awk '{print $" + sys.argv[2] + "}'").readlines()
print result[0][:-1]
