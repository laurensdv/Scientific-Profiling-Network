<?php
function tweetGrabeeterTripleCreation(&$row, &$tweets, &$link) {
        $id = $row['id'];
        $geo = $row['geolocation_id'];
        $src = $row['source_id'];
        //TRIPLES CREATION
        $tweettree = new TripleTree(GRABTWEET . $id);
        $tweettree->addProperty(RDF . 'type', SIOCT . 'MicroblogPost');
        $tweettree->addProperty(SIOC . 'content', $row['text']);
        $date = $row['tweet_created_at'];
        $date = new DateTime($date);
        $date = '"' . $date->format('Y-m-d\TH:i:sP') . '"^^xsd:dateTime';
        $tweettree->addProperty(DCTERMS . 'created', $date);
        if ($row['in_reply_to_status_id'] != null)
            $tweettree->addProperty(SIOC . 'reply_of', $row['in_reply_to_status_id']);

        //SOURCE
        $sql = sprintf("SELECT label, url FROM tweet_source WHERE id='%s'",
                        mysql_real_escape_string($src));
        $result = mysql_query($sql, $link);
        $source = mysql_fetch_assoc($result);
        $src_string = $source['label']; //.' '.$source['url'];
        preg_match('/href="([^"]*)"/i', $src_string, $matches);
        if (count($matches) == 0)
            $tweettree->addProperty(DCTERMS . 'source', $src_string);
        else
            $tweettree->addProperty(DCTERMS . 'source', $matches[1]);

        //GEOLOCATION IF AVAILABLE
        if (!$geo == null) {
            $sql = sprintf("SELECT latitude, longitude FROM tweet_geo_location WHERE id='%s'",
                            mysql_real_escape_string($geo));
            $result = mysql_query($sql, $link);
            $location = mysql_fetch_assoc($result);
            $loc_string = "http://ws.geonames.org/findNearby?lat=" . $location['latitude'] . "&lng=" . $location['longitude'];
            $tweettree->addProperty(DCTERMS . 'spatial', $loc_string);
            $tweettree->addProperty(POS . 'lat', $location['latitude']);
            $tweettree->addProperty(POS . 'long', $location['longitude']);
        }

        $tweets['g_' . $id] = $tweettree;
    }

function tweetTwitterTripleCreation(&$status, &$user, &$tweets) {
    $id = $status['id'];
    $geo = $status['place'];
    $src = $status['source'];
    //TRIPLES CREATION
    $tweettree = new TripleTree(TWITTER_BASE . $user . "/statuses/" . $id);
    $tweettree->addProperty(RDF . 'type', SIOCT . 'MicroblogPost');
    $tweettree->addProperty(SIOC . 'content', $status['text']);
    //$date = $status['created_at'];
    $date = new DateTime($status['created_at']);
    $date = '"' . $date->format('Y-m-d\TH:i:sP') . '"^^xsd:dateTime';
    $tweettree->addProperty(DCTERMS . 'created', $date);
    if ($status['in_reply_to_status_id'] != null)
        $tweettree->addProperty(SIOC . 'reply_of', $status['in_reply_to_status_id']);

    preg_match('/href="([^"]*)"/i', $src, $matches);
    if (count($matches) == 0)
        $tweettree->addProperty(DCTERMS . 'source', $src);
    else
        $tweettree->addProperty(DCTERMS . 'source', $matches[1]);

    //GEOLOCATION IF AVAILABLE
    if ($geo != null && array_key_exists('bounding_box',$geo)) {        
        //print_r($geo['bounding_box']['coordinates']);
        $geolocations = $geo['bounding_box']['coordinates'][0];
        
        $n = count($geolocations);
        $longitude = 0;
        $latitude = 0;
        foreach($geolocations as $geolocation) {
            $longitude += (float)$geolocation[0];
            $latitude += (float)$geolocation[1];
        }
        $longitude = (float)$longitude/$n;
        $latitude = (float)$latitude/$n;
        //echo $longitude;
        //echo $latitude;
        $tweettree->addProperty(GN.'countryCode',$geo['country_code']);
        $tweettree->addProperty(GN.'country',$geo['country']);
        $tweettree->addProperty(POS.'long',$longitude);
        $tweettree->addProperty(POS.'lat',$latitude);
        $tweettree->addProperty(DCTERMS . 'spatial', $geo['full_name']);
    }
    $tweets['t_' . $id] = $tweettree;
}

function createTweetsTripleTree($user,$start_date=null) {
    if(USE_GRABEETER==false) return tweetsFromTwitter($user);
    $link = mysql_connect(GRAB_SOCKET, GRAB_USER, GRAB_PASS);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }

    if (!mysql_select_db(DB, $link)) {
        echo 'Could not select database';
        exit;
    }

    $uid = null;
    $tweets = array();
    $sql = null;

    $sql = sprintf("SELECT id, statuses_count FROM tweet_user WHERE screen_name='%s'",
                    mysql_real_escape_string($user));


    $result = mysql_query($sql, $link);

    if (!$result) {
        echo "DB Error, could not query the database\n";
        echo 'MySQL Error: ' . mysql_error();
        exit;
    }

    while ($row = mysql_fetch_assoc($result)) {
        $uid = $row['id'];
        $status_count = $row['statuses_count'];
    }

    if ($uid == null) {
        echo "# User not found in Grabeteer";
        return tweetsFromTwitter($user);
    }

     if($start_date == null) {
    $sql = sprintf("SELECT * FROM tweet WHERE user_id='%s' ORDER BY  `tweet_created_at` DESC LIMIT ".TWEETS_LIMIT,
                    mysql_real_escape_string($uid));
     } else {
         $sql = sprintf("SELECT * FROM tweet WHERE user_id='%s' AND `tweet_created_at` > '".$start_date."' ORDER BY  `tweet_created_at` DESC LIMIT ".TWEETS_LIMIT,
                    mysql_real_escape_string($uid));
     }


    $result_tweets = mysql_query($sql, $link);

    if (!$result_tweets) {
        echo "DB Error, could not query the database\n";
        echo 'MySQL Error: ' . mysql_error();
        exit;
    }

    // test to see if threading is available
    if (!Thread::available() || DEBUG == true) {
        while ($row = mysql_fetch_assoc($result_tweets)) {
            tweetGrabeeterTripleCreation($row, $tweets, $link);
        }
    } else {
        $threads = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result_tweets)) {
            $threads[$i] = new Thread('tweetGrabeeterTripleCreation');
            $threads[$i]->start($row, $tweets, $link);
            ++$i;
        }
        // wait for all the threads to finish
        while (!empty($threads)) {
            foreach ($threads as $index => $thread) {
                if (!$thread->isAlive()) {
                    unset($threads[$index]);
                }
            }
            // let the CPU do its work
            sleep(1);
        }
    }
    //print_r($tweets);

    mysql_free_result($result);
    mysql_free_result($result_tweets);
    mysql_close($link);
    return $tweets;
}

function tweetsFromTwitter($user) {
    // create instances
    $tweets = array();
        // create instance
	$twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

	// set tokens
	$twitter->setOAuthToken('77273706-Wa8wXj7JAly0nQ2RMvn74CeiLM4B88pkHDaYIrvMz');
	$twitter->setOAuthTokenSecret('wc8gfhWzUj3V6JGRZ8UmJak1XKyzWlWZHSc3D2of9E');

    $usertimeline = $twitter->statusesUserTimeLine($user);
    //print_r($usertimeline);
    // test to see if threading is available
    if (!Thread::available() || DEBUG == true) {
        foreach ($usertimeline as $status) {
            tweetTwitterTripleCreation($status, $user, $tweets);
        }
    } else {
        $threads = array();
        $i = 0;
        foreach ($usertimeline as $status) {
            $threads[$i] = new Thread('tweetTwitterTripleCreation');
            $threads[$i]->start($status, $user, $tweets);
            ++$i;
        }
        // wait for all the threads to finish
        while (!empty($threads)) {
            foreach ($threads as $index => $thread) {
                if (!$thread->isAlive()) {
                    unset($threads[$index]);
                }
            }
            // let the CPU do its work
            sleep(1);
        }
    }

    return $tweets;
}


?>