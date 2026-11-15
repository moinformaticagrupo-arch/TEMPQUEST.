#!/bin/sh
set -eu
PORT=$(printenv PORT)
if [ -z "$PORT" ]; then PORT=10000; fi
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
