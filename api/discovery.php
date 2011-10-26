<?php
ob_start();
header('Content-type: application/json');
header("Content-Disposition: inline; filename=discovery.json");
header(200);
ob_flush();

require_once('../includes.php');
require_once('../SMNIncludes.php');

if( !ini_get('safe_mode') ){
	set_time_limit(180);
        ini_set('memory_limit', '512M');
}

if ($_GET != null && array_key_exists('find',$_GET)) {
    if(strcasecmp($_GET['find'],'persons')==0 && array_key_exists('user',$_GET)) {
        $user=$_GET['user'];
        $max_distance=array_key_exists('op',$_GET)?$_GET['op']:null;
        $lat=array_key_exists('lat', $_GET)?$_GET['lat']:null;
        $long=array_key_exists('long', $_GET)?$_GET['long']:null;
        $results = SMNUserFinder::findRelatedUsers($user, $max_distance, $lat, $long);
        echo json_encode($results);
        ob_flush();
    }
    if(strcasecmp($_GET['find'],'events')==0 && array_key_exists('user',$_GET)) {
        $user=$_GET['user'];
        $max_distance=array_key_exists('op',$_GET)?$_GET['op']:null;
        $lat=array_key_exists('lat', $_GET)?$_GET['lat']:null;
        $long=array_key_exists('long', $_GET)?$_GET['long']:null;
        $date_begin=array_key_exists('from', $_GET)?$_GET['from']:null;
        $date_end=array_key_exists('to', $_GET)?$_GET['to']:null;

        $results = SMNEventFinder::findRelatedEvents($user, $date_begin,$date_end,$max_distance, $lat, $long);
        echo json_encode($results);
        ob_flush();
    }
    if(strcasecmp($_GET['find'],'popular_users')==0) {
        $users = SMNUserFinder::mostPopularUsersFast();
        echo json_encode($users);
        ob_flush();
    }
    if(strcasecmp($_GET['find'],'popular_mentions')==0) {
        $users = SMNUserFinder::mostPopularMentionsFast();
        echo json_encode($users);
        ob_flush();
    }
    if(strcasecmp($_GET['find'],'popular_events')==0) {
        $events = SMNEventFinder::mostPopularEvents();
        echo json_encode($events);
        ob_flush();
    }
} else echo "Invalid arguments";
flush();
ob_end_flush();
?>
