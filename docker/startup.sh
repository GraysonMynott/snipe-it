#!/bin/bash

# fix key if needed
if [ -z "$APP_KEY" ]
then
  echo "Please re-run this container with an environment variable \$APP_KEY"
  echo "An example APP_KEY you could use is: "
  /var/www/html/artisan key:generate --show
  exit
fi

if [ -f /var/lib/snipeit/ssl/snipeit-ssl.crt -a -f /var/lib/snipeit/ssl/snipeit-ssl.key ]
then
  a2enmod ssl
else
  a2dismod ssl
fi

# create data directories
# Note: Keep in sync with expected directories by the app
# https://github.com/snipe/snipe-it/blob/master/app/Console/Commands/RestoreFromBackup.php#L232
for dir in \
  'data/private_uploads' \
  'data/private_uploads/assets' \
  'data/private_uploads/patches' \
  'data/private_uploads/eula-pdfs' \
  'data/private_uploads/imports' \
  'data/private_uploads/assetmodels' \
  'data/private_uploads/users' \
  'data/private_uploads/licenses' \
  'data/private_uploads/signatures' \
  'data/uploads/assets' \
  'data/uploads/avatars' \
  'data/uploads/barcodes' \
  'data/uploads/categories' \
  'data/uploads/companies' \
  'data/uploads/locations' \
  'data/uploads/manufacturers' \
  'data/uploads/models' \
  'dumps' \
  'keys'
do
  [ ! -d "/var/lib/snipeit/$dir" ] && mkdir -p "/var/lib/snipeit/$dir"
done

chown -R docker:root /var/lib/snipeit/data/*
chown -R docker:root /var/lib/snipeit/dumps
chown -R docker:root /var/lib/snipeit/keys
chown -R docker:root /var/www/html/storage/framework/cache
touch /var/www/html/storage/logs/laravel.log
chown -R docker:root /var/www/html/storage/logs

# Fix php settings
if [ -v "PHP_UPLOAD_LIMIT" ]
then
    echo "Changing upload limit to ${PHP_UPLOAD_LIMIT}"
    sed -i "s/^upload_max_filesize.*/upload_max_filesize = ${PHP_UPLOAD_LIMIT}M/" /etc/php/*/apache2/php.ini
    sed -i "s/^post_max_size.*/post_max_size = ${PHP_UPLOAD_LIMIT}M/" /etc/php/*/apache2/php.ini
fi

# If the Oauth DB files are not present copy the vendor files over to the db migrations
if [ ! ls /var/www/html/database/migrations/*create_oauth* 1> /dev/null 2>&1 ]
then
  cp -ax /var/www/html/vendor/laravel/passport/database/migrations/* /var/www/html/database/migrations/
fi

if [ "$SESSION_DRIVER" = "database" ]
then
  cp -ax /var/www/html/vendor/laravel/framework/src/Illuminate/Session/Console/stubs/database.stub /var/www/html/database/migrations/2021_05_06_0000_create_sessions_table.php
fi

php artisan migrate --force
php artisan db:seed
php artisan config:clear
php artisan config:cache

exec supervisord -c /supervisord.conf
