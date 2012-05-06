#! /bin/bash
php src/app/console doctrine:schema:create
mv src/app/config/parameters.ini.dist src/app/config/parameters.ini
php src/bin/vendors install
sudo chmod -R 777 src/app/logs
sudo chmod -R 777 src/app/cache
