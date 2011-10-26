<?php
function __autoload($class_name) {
    include $class_name . '.php';
}

require_once dirname(__FILE__).'/loaders/loadfunctions.php';
require_once '../includes.php';

if( !ini_get('safe_mode') ){
	set_time_limit(0);
}

if (array_key_exists('user', $_GET))
    loadUser($_GET['user']);
else
    loadAllGrabeeterUsers();
?>