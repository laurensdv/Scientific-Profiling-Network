<?php
function createUserTripleTree($user) {
        if(USE_GRABEETER==false) return profileFromTwitter($user);
	$link = mysql_connect(GRAB_SOCKET, GRAB_USER, GRAB_PASS);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}

	if (!mysql_select_db(DB, $link)) {
		echo 'Could not select database';
		exit;
	}

	$uid =  null;
	$info = array();

	$sql = sprintf("SELECT * FROM tweet_user WHERE screen_name='%s'",
	mysql_real_escape_string($user));

	$result = mysql_query($sql, $link);

	if (!$result) {
		echo "DB Error, could not query the database\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}
        $row = array();
	while ($row = mysql_fetch_assoc($result)) {
		$uid = $row['id'];
                $userdata = $row;
	}

	if ($uid==null) {
		//echo "User not found in Grabeteer";
		return profileFromTwitter($user);
	}

        //TODO: ADD followers!
        //$userfollows = "Follows ".$userdata['friends_count']." users.";
        try {
            //$userfollows = friendsFromTwitter($user);
        } catch (TwitterException $e) {
            echo "Error: ".$e->getMessage().PHP_EOL.PHP_EOL.$e->getTrace().PHP_EOL;
        }

	$usertree = new TripleTree(TWITTER_URI.$userdata['twitter_user_id']);

	//Basic Properties
	$usertree->addProperty(RDF.'type',SIOC.'UserAccount');
	$usertree->addProperty(RDFS.'seeAlso','http://twitter.com/'.$user);
	$usertree->addProperty(FOAF.'name', $userdata['name']);
	$usertree->addProperty(FOAF.'nickname', $userdata['screen_name']);
	$usertree->addProperty(FOAF.'homepage', $userdata['url']);
	$usertree->addProperty(FOAF.'img', $userdata['profile_image_url']);
	$usertree->addProperty(DCTERMS.'spatial', $userdata['location']);
	$usertree->addProperty(DCTERMS.'description', $userdata['description']);

	//Friends
	$usertree->addProperty(FOAF.'follows', $userdata['friends_count']);

	return $usertree;
}

function profileFromTwitter($user) {
        // create instance
	$twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

	// set tokens
	$twitter->setOAuthToken('77273706-Wa8wXj7JAly0nQ2RMvn74CeiLM4B88pkHDaYIrvMz');
	$twitter->setOAuthTokenSecret('wc8gfhWzUj3V6JGRZ8UmJak1XKyzWlWZHSc3D2of9E');

	//$user = $_GET['user'];
	$userdata = $twitter->usersShow($user);
	$userfriends = $twitter->friendsIds($user);
	$userfollows = array();

	foreach ($userfriends as $userfriend) {
		$userfollows[$userfriend] = TWITTER_URI.$userfriend;
	}

	$usertree = new TripleTree(TWITTER_URI.$userdata['id']);

	//Basic Properties
	$usertree->addProperty(RDF.'type',SIOC.'UserAccount');
	$usertree->addProperty(RDFS.'seeAlso','http://twitter.com/'.$user);
	$usertree->addProperty(FOAF.'name', $userdata['name']);
	$usertree->addProperty(FOAF.'nickname', $userdata['screen_name']);
	$usertree->addProperty(FOAF.'homepage', $userdata['url']);
	$usertree->addProperty(FOAF.'img', $userdata['profile_image_url']);
	$usertree->addProperty(DCTERMS.'spatial', $userdata['location']);
	$usertree->addProperty(DCTERMS.'description', $userdata['description']);

	//Friends
	$usertree->addProperty(SIOC.'follows', $userfollows);

	return $usertree;
}
?>