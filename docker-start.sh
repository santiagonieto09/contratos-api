#!/bin/sh

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

php -d variables_order=EGPCS -S 0.0.0.0:${PORT:-8080} public/index.php
