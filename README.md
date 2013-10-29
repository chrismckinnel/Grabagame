Grab a game
===========

[![Build Status](https://secure.travis-ci.org/chrismckinnel/Grabagame.png?branch=develop)](http://travis-ci.org/chrismckinnel/Grabagame)

Set up project to run locally
-----------------------------

These instructions assume you have a working stack of Apache, PHP and MySQL.

1. Clone the git repo to your local machine

    git clone https://github.com/chrismckinnel/Grabagame.git

2. Create a MySQL database and user for Grabagame

    a. You'll also need to create a `grabagame_test` database. Give all 
       privileges to one user on both databases. This database is used to 
       run the unit tests

3. Copy `src/app/config/parameters.ini.dist` to `parameters.ini`

4. Edit `parameters.ini` and fill in 
    a. Database name, user and password
    b. Secret
    c. Base logging URL (this should point to src/app/logs)

5. Install the vendors with:

    php bin/vendors install

6. Once you've installed the vendors, you should be able to run 

    php app/console

7. To run the projects tests, you'll need 
   
    a. [PHPUnit][0] -- Install via PEAR
    b. [DBUnit][1] (`pear install phpunit/dbUnit)

    phpunit -c app


[0]: http://phpunit.de/manual/3.7/en/installation.html
[1]: http://phpunit.de/manual/3.7/en/database.html
