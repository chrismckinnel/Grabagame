#! /bin/bash
php src/bin/vendors install
sudo chmod -R 777 src/app/logs
sudo chmod -R 777 src/app/cache
