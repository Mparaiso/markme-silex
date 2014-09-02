MARK.ME
=======

[![Build Status](https://travis-ci.org/Mparaiso/markme-silex.svg?branch=master)](https://travis-ci.org/Mparaiso/markme-silex)

Online bookmark manager
-----------------------

[![markme](http://aikah.online.fr/images/markme.jpg)](https://markme.herokuapp.com)

## author : M.Paraiso

inspired by bookmark.ly : http://bookmarkly.com/

## LIVE DEMO : https://markme.herokuapp.com/

+ Bookmark sites fast
+ Add description and tags
+ Import and Export your bookmarks from and to popular browsers
+ bookmark's screenshots
+ Search and filter through your bookmarks
+ Access your bookmarks anywhere!

#### INSTALLATION

##### requirements

+ an apache webserver
+ php 5.3.*
+ mysql database

##### configuration

+ set up a virtualhost on the server

+ declare the following envirronment variables ( in a .htaccess file for instance ):

    + MARKME_DB_DRIVER ( should be pdo_mysql )  
    + MARKME_DB_DATABASE_NAME (database name)
    + MARKME_DB_HOST ( exemple : locahost )
    + MARKME_DB_USERNAME (database username )
    + MARKME_DB_PASSWORD (database password )
    + MARKME_SALT a salt for password encryption ( a sentence , whatever )
    + MARKME_ENVIRONMENT production or development

+ the webroot is the `web` folder

+ get composer
    + http://getcomposer.org/
    + in the repository folder , install composer packages : 
        php /path-to-composer/composer.phar install

+ once,the environment variables are configured, in a console ,in the repo folder 
    
        php console.php  orm:schema-tool:create --force

to create the database.

+ create an account and login !

###### Why

+ Help learn Silex Framework : http://silex.sensiolabs.org
+ Help learn AngularJS Framework : http://angularjs.org/
+ Help learn AngularJS / Twitter Bootstrap integration : http://twitter.github.com/bootstrap/



    