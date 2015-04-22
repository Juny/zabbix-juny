#!/bin/bash
mysqladmin extended-status|grep "$1 "|awk '{print $4}'
