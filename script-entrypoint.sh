#!/bin/bash

cd /var/www/doc-viewer

php artisan serve

exec "$@"
