<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('SMNIncludes.php');
require_once('includes.php');

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

echo "Populating \n";

$existing_users = SMNUserQueries::allUserUris();
$popular_friends = SMNUserFinder::mostPopularUsersFast();
$popular_mentions = SMNUserFinder::mostPopularMentionsFast();

foreach($existing_users as $existing_user) {
    if(array_key_exists($existing_user, $popular_friends)) {
            unset($popular_friends[$existing_user]);
}
foreach(SMNUserQueries::allUsers() as $existing_user) {
    if(array_key_exists($existing_user, $popular_mentions))
        unset($popular_mentions[$existing_user]);
    }
}
$max = count($popular_friends);
$nm = count($popular_mentions);

$friend_uris = array_keys($popular_friends);

$trimmed_friends = trim_array($friend_uris, $max>9?9:$max);
$trimmed_mentions = trim_array(array_keys($popular_mentions), $nm>20?20:$max);

if($max>10) {
    $rand = rand(10, $max);
    $trimmed_friends[] = $friend_uris[$rand];
}

$friendslist = array();

echo "Popular Conferences \n";

$conferences = SMNEventFinder::mostPopularEvents();
$twitter = new Twitter("32FoG0kITEE2Pq44Fi7Hzg", "Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM");
$persons = array();

// set tokens
$twitter->setOAuthToken('77273706-Wa8wXj7JAly0nQ2RMvn74CeiLM4B88pkHDaYIrvMz');
$twitter->setOAuthTokenSecret('wc8gfhWzUj3V6JGRZ8UmJak1XKyzWlWZHSc3D2of9E');

try {
foreach($conferences as $conference => $count) {    
    $people = $twitter->usersSearch($conference);
        foreach($people as $key => $person) {
            $persons[] = $person['screen_name'];
            echo $person['screen_name'].PHP_EOL;
        }

}

} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
}

foreach($persons as $person) {
    echo "Registering: ".$person.PHP_EOL;
    registerUser($person);
}



echo "Popular Mentions: \n";
foreach($trimmed_mentions as $mention) {
    echo "Registering: ".$mention.PHP_EOL;
    registerUser($mention);
}

$friendsinfo = getMultiHttp($trimmed_friends);
if(!is_array($friendsinfo)||$friendsinfo==null) {
    echo "Can not retrieve screen names right now";
    exit(0);
}
foreach ($friendsinfo as $key => $friendinfo) {
	$friendall = json_decode($friendinfo,true);
        if(!is_array($friendall)||!array_key_exists('screen_name', $friendall)) exit(0);
            $screenname = $friendall['screen_name'];
	    $friendslist[$key] = $screenname;
        }

echo "Popular Friends: \n";

print_r($trimmed_friends);

foreach($friendslist as $friend_screen_name) {
    echo "Registering: ".$friend_screen_name.PHP_EOL;
    registerUser($friend_screen_name);
}


?>
