<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-type: application/json');
header("Content-Disposition: inline; filename=profile.json");

if( !ini_get('safe_mode') ){
	set_time_limit(180);
        ini_set('memory_limit', '128M');
}

require_once('../includes.php');
require_once('../SMNIncludes.php');

if ($_GET != null) {
    if(array_key_exists('uri',$_GET)) {
        $_GET['user'] = SMNUserQueries::userScreenNameByUri($_GET['uri']);
    }
    if(array_key_exists('user',$_GET)) {
        $user = $_GET['user'];
        //TODO: check if user exists
        $profile = new SMNProfile($user);
        $profile_array = array();
        $profile_array['uri'] = $profile->showUserUri();
        if($profile_array['uri']==false) {
            echo 'User not found, please register first!';
            exit(0);
        }
       
        $profile_array['name'] = $profile->showName();
        $profile_array['screen_name'] = $profile->showScreenName();
        $profile_array['location'] = $profile->showLocation();
        $profile_array['image']=$profile->showImage();
        $profile_array['description']=$profile->showDescription();
        $profile_array['friends'] = $profile->showFriendUris();
        $profile_array['mentions'] = $profile->showMentions();
        $profile_array['scientific_events']=$profile->showEvents();
        $profile_array['persons']=$profile->showEntities('Person');
        $profile_array['places']=$profile->showEntities('Place');
        $profile_array['general_events']=$profile->showEntities('Event');
        $profile_array['organisations']=$profile->showEntities('Organisation');
        $profile_array['tags']=$profile->showInterests();
        if(array_key_exists('tweet', $_GET))  $profile_array['latest_tweet']=$profile->showLatestTweet();

        $profile_json = json_encode($profile_array);
        
        echo $profile_json;
    }
} else echo "Invalid arguments";
?>
