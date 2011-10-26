<?php
require_once 'includes.php';
require_once 'SMNIncludes.php';

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

$existing_users = SMNUserQueries::allUsers();
loadGrabeeterUsers($existing_users);
?>
