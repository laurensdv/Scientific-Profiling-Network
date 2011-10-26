<?php
require_once '../../includes.php';

if( !ini_get('safe_mode') ){ 
	set_time_limit(120); 
} 

if(!array_key_exists('user', $_GET)) 
	echo 'Error: No user parameter given';
else {
	ob_start();
	$userparameter=$_GET['user'];
	//Build the document
	echo '# Extracting user profile'.PHP_EOL;
	ob_flush();
	flush();
	$user = new UserTurtler($userparameter,true,true);
	echo '# User profile extracted'.PHP_EOL;
	ob_flush();
	flush();
	echo '# Friends'.PHP_EOL;
	print_r($user->getUserFriends());
	echo '# User'.PHP_EOL;
	echo $user->getUserProfile();
	echo '# Timeline'.PHP_EOL;
	echo $user->getUserTimeline();
	echo '# Tweets'.PHP_EOL;
        echo $user->getAllTweetsOld();
	echo '# Tags'.PHP_EOL;
	echo $user->getAllTags();
	echo '# Locations'.PHP_EOL;
	echo $user->getAllGeoLocations();
	ob_end_flush();
}

//Build the document


?>