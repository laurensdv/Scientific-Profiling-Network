<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('includes.php');
require_once('SMNIncludes.php');

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

$existing_users = SMNUserQueries::allUsers();

$fh = fopen(dirname(__FILE__)."/api/cached_profiles/"."allusers.json", 'w');

$json_users = json_encode($existing_users);

fwrite($fh, $json_users);
fclose($fh);
echo "created alluserlist".PHP_EOL;


foreach($existing_users as $key => $user) {
        try {
        $profile = new SMNProfile($user);
        $profile_array = array();
        $profile_array['uri'] = $profile->showUserUri();
        $uri = $profile_array['uri'];
        $shortversion = substr($uri, strrpos($uri, "=")+1);
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
        $profile_array['tags']=$profile->showInterests();

        $profile_json = json_encode($profile_array);

        $ourFileName = urlencode($shortversion).".json";
        echo "created".$ourFileName.PHP_EOL;
        $fh = fopen(dirname(__FILE__)."/api/cached_profiles/".$ourFileName, 'w');
        fwrite($fh, $profile_json);
        fclose($fh);
         } catch (Exception $exception) {
            echo $exception->getMessage().PHP_EOL;
        }
}


?>
