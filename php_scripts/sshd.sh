#!/bin/sh
sudo lsof -i -n | egrep '\<sshd\>'
