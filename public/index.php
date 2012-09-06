<?php
putenv('MONGODB_SERVER=mongodb://camus:defender@alex.mongohq.com:10079/mparaiso');
putenv('MONGODB_DATABASE=mpraiso');
define('ROOT',dirname(__DIR__));
$app = require(dirname(__DIR__)."/App/bootstrap.php");