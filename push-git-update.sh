#!/bin/bash
/usr/bin/git add .
/usr/bin/git commit -m "-"
GIT_TRACE=1 /usr/bin/git push bigls master

