#! /bin/bash
sudo pear install phpunit/DbUnit
cd src
mv app/config/parameters.ini.dist src/app/config/parameters.ini
php bin/vendors install
php app/console doctrine:schema:create
sudo chmod -R 777 app/logs
sudo chmod -R 777 app/cache
