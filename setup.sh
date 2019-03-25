#!/bin/bash

# Grant database permission
chmod o+w ./sqlite.db

# Entrypoint
/usr/sbin/apache2ctl -D FOREGROUND