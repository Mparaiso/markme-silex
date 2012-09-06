<?php
if($_SERVER['HTTP_HOST']!='mparaiso.local'):
	putenv('MONGODB_DATABASE=mpraiso');
	putenv('MONGODB_SERVER=mongodb://camus:defender@alex.mongohq.com:10079/mparaiso');
endif;
define('ROOT',dirname(__DIR__));
$app = require(dirname(__DIR__)."/App/bootstrap.php");