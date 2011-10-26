<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

require_once('includes.php');
require_once('SMNIncludes.php');



$ourFileName = "discovery.json";
$fh = fopen(dirname(__FILE__)."/cached/".$ourFileName, 'w');

$results = SMNUserFinder::findRelatedUsers("mstrohm", null, null, null);
$json_results = json_encode($results);

fwrite($fh, $json_results);
fclose($fh);

$tagfriends = $results["tags"];
$mentionfriends = $results["mentions"];
$conferencefriends = $results["scientific_events"];

foreach($tagfriends as $key => $index) {
        try {
        $shortversion = substr($key, strrpos($key, "=")+1);
        $user = SMNUserQueries::userScreenNameByUri($key);
        //TODO: check if user exists
        $profile = new SMNProfile($user);
        $profile_array = array();
        $profile_array['uri'] = $profile->showUserUri();
        if($profile_array['uri']==false) {
            echo 'User not found, please register first!';
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
        $profile_array['tags']=$profile->showTags();

        $profile_json = json_encode($profile_array);

        $ourFileName = dirname(__FILE__)."/cached/profile".$shortversion.".json";
        $fh = fopen($ourFileName, 'w');
        fwrite($fh, $profile_json);
        fclose($fh);
        } catch (Exception $exception) {
            echo $exception->getMessage().PHP_EOL;
        }
}
foreach($mentionfriends as $key => $index) {
        try {
        $shortversion = substr($key, strrpos($key, "=")+1);
        $user = SMNUserQueries::userScreenNameByUri($key);
        //TODO: check if user exists
        $profile = new SMNProfile($user);
        $profile_array = array();
        $profile_array['uri'] = $profile->showUserUri();
        if($profile_array['uri']==false) {
            echo 'User not found, please register first!';
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
        $profile_array['tags']=$profile->showTags();

        $profile_json = json_encode($profile_array);

        $profile_json = json_encode($profile_array);

        $ourFileName = "profile".$shortversion.".json";
        $fh = fopen(dirname(__FILE__)."/cached/".$ourFileName, 'w');
        fwrite($fh, $profile_json);
        fclose($fh);
         } catch (Exception $exception) {
            echo $exception->getMessage().PHP_EOL;
        }
}
foreach($conferencefriends as $key => $index) {
        try {
        $shortversion = substr($key, strrpos($key, "=")+1);
        $user = SMNUserQueries::userScreenNameByUri($key);
        //TODO: check if user exists
        $profile = new SMNProfile($user);
        $profile_array = array();
        $profile_array['uri'] = $profile->showUserUri();
        if($profile_array['uri']==false) {
            echo 'User not found, please register first!';
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
        $profile_array['tags']=$profile->showTags();

        $profile_json = json_encode($profile_array);

        $ourFileName = "profile".$shortversion.".json";
        $fh = fopen(dirname(__FILE__)."/cached/".$ourFileName, 'w');
        fwrite($fh, $profile_json);
        fclose($fh);
         } catch (Exception $exception) {
            echo $exception->getMessage().PHP_EOL;
        }
}
?>
