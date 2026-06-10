#!/bin/sh

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

sed "s/__PORT__/${PORT:-8080}/" /etc/nginx/conf.d/default.conf > /tmp/nginx.conf && \
cp /tmp/nginx.conf /etc/nginx/conf.d/default.conf

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
