#! /bin/bash
pyrus install http://phptal.org/latest.tar.gz
phpenv rehash
pyrus install phpunit/DbUnit
sudo mkdir /var/log/grabagame
sudo mkdir /var/cache/grabagame
sudo chmod -R 777 /var/log/grabagame
sudo chmod -R 777 /var/cache/grabagame
cd src
mv app/config/parameters.ini.travis app/config/parameters.ini
cat app/config/parameters.ini
php bin/vendors install
php app/console doctrine:schema:create
sudo chmod -R 777 app/logs
sudo chmod -R 777 app/cache
