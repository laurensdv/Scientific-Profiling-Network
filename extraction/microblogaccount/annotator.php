<?php
function annotateUser($user) {
	$user_annotated = array();
	$tweetids=array();
	$geolocations = array();
	$tags = array();

	$usertree=createUserTripleTree($user);
	$tweettree=createTweetsTripleTree($user);
	$i=0;

	$useraccount = $usertree->getRoot();
	$userdata = $usertree->getProperties();
	$userproperties = $usertree->getProperties();
	$usertimeline = TIMELINE_URI.$user;

	//Create a timeline that connects the user and its tweets
	$timelinetree = new TripleTree($usertimeline);
	$timelinetree->addProperty(RDF.'type',SIOCT.'Microblog');
	$timelinetree->addProperty(DCTERMS.'title','Twitter Timeline - '.$user);
	$timelinetree->addProperty(DCTERMS.'description','Twitter updates from '.$user);
	$timelinetree->addProperty(RDFS.'link',$userproperties[RDFS.'SeeAlso']);
	
	//Create a person that holds the user account
	$name = $userdata[FOAF.'name'];
	$person=new TripleTree(PERSONS_URI.urlencode($name));
	$person->addProperty(RDF.'type',FOAF.'Person');
	$person->addProperty(FOAF.'name', $name);
	$person->addProperty(FOAF.'account', $useraccount);
	$tags['person']=$person;
	
	//Annotate the user and its tweets to match the timeline
	foreach ($tweettree as $tweet) {
		$tweetprops=$tweet->getProperties();
		$tweetcontent=$tweetprops[SIOC.'content'];
		$tweet->addProperty(SIOC.'has_container', $usertimeline);
		$tweet->addProperty(SIOC.'has_creator', $useraccount);
		matchMentions($tweet, $tweetcontent);
		matchUrls($tweet, $tweetcontent);
		$tags=array_merge($tags,matchHashTags($tweet, $tweetcontent));
		$tweetids[$i++]=$tweet->getRoot();
	}

	$timelinetree->addProperty(SIOC.'container_of', $tweetids);
	$timelinetree->addProperty(SIOC.'has_creator', $useraccount);
	$tweetids['timeline']=$usertimeline;
	$usertree->addProperty(SIOC.'creator_of', $tweetids);

	//Add Geo Information
	
	$geolocations = geoTag($tweettree);
	
	$userlocation = $userproperties[DCTERMS."spatial"];
	$usergeolocation = geoAnnotate($userlocation);
	$geo_url = preg_replace('/\s*/m' , '' , GEO_URI.$userlocation);
	if(!$usergeolocation==null) {
		$usertree->addProperty(DCTERMS."spatial",$usergeolocation->getRoot());
		$geolocations[$geo_url]=$usergeolocation;
	}
	
	//Add automatic content tagging
	
	$tags = array_merge($tags,tagTweets($tweettree, $usertree, $timelinetree));
	

	//Add Conference Information

	//Combine all annotated result trees;
	$user_annotated['user']=$usertree;
	$user_annotated['timeline']=$timelinetree;
	$user_annotated['tweets']=$tweettree;
	if(count($geolocations)>0) $user_annotated['geolocations']=$geolocations;
	if(count($tags)>0) $user_annotated['tags']=$tags;

	return $user_annotated;
}

//$user as a string that represents the screen_name
function annotateUserProfile(&$user) {
	$usertree=createUserTripleTree($user);
	return $usertree;
}

//$user as a string that represents the screen_name
function annotateUserTweets(&$user,&$start_date=null) {
	$tweettree=createTweetsTripleTree($user,$start_date);
	return $tweettree;
}

//Require $tweets in the form of an array of TripleTrees
function annotateWithGeoData(&$userprofile,&$tweets) {
	//Get the necessary URI's
	$userproperties = $userprofile->getProperties();
	
	$geolocations = geoTag($tweets);
	
	$userlocation = $userproperties[DCTERMS."spatial"];
	$usergeolocation = geoAnnotate($userlocation);
	$geo_url = preg_replace('/\s*/m' , '' , GEO_URI.$userlocation);
	if(!$usergeolocation==null) {
		$userprofile->addProperty(DCTERMS."spatial",$usergeolocation->getRoot());
		$geolocations[$geo_url]=$usergeolocation;
	}
	
	return $geolocations;	
}

//Require $tweets in the form of an array of TripleTrees
//Require $userprofile in the form of a TripleTree
//Resuire $tags in the form as an array of tags (can be empty)
function annotateUserTimeLine(&$usertree,&$tweettree,&$tags) {
	//Get the necessary URI's
	$userproperties = $usertree->getProperties();
	$user = $userproperties[FOAF.'nickname'];
	$usertimeline = TIMELINE_URI.$user;
	$useraccount = $usertree->getRoot();
	$userdata = $usertree->getProperties();
	
	//Create a timeline that connects the user and its tweets
	$timelinetree = new TripleTree($usertimeline);
	$timelinetree->addProperty(RDF.'type',SIOCT.'Microblog');
	$timelinetree->addProperty(DCTERMS.'title','Twitter Timeline - '.$user);
	$timelinetree->addProperty(DCTERMS.'description','Twitter updates from '.$user);
	$timelinetree->addProperty(RDFS.'link',$userproperties[RDFS.'seeAlso']);

	//Create a person that holds the user account
	$name = $userdata[FOAF.'name'];
	$person=new TripleTree(PERSONS_URI.urlencode($name));
	$person->addProperty(RDF.'type',FOAF.'Person');
	$person->addProperty(FOAF.'name', $name);
	$person->addProperty(FOAF.'account', $useraccount);
	$tags['person']=$person;
	
	//Annotate the user and its tweets to match the timeline
	$i = 0;
	$tweetids=array();
	foreach ($tweettree as $tweet) {
		$tweetprops=$tweet->getProperties();
		$tweetcontent=$tweetprops[SIOC.'content'];
		$tweet->addProperty(SIOC.'has_container', $usertimeline);
		$tweet->addProperty(SIOC.'has_creator', $useraccount);
		matchMentions($tweet, $tweetcontent);
		matchUrls($tweet, $tweetcontent);
		$tags=array_merge($tags,matchHashTags($tweet, $tweetcontent));
		$tweetids[$i++]=$tweet->getRoot();
	}

	if(EXPLICIT) $timelinetree->addProperty(SIOC.'container_of', $tweetids);
	$timelinetree->addProperty(SIOC.'has_creator', $useraccount);
	$tweetids['timeline']=$usertimeline;
	if(EXPLICIT) $usertree->addProperty(SIOC.'creator_of', $tweetids);
	
	return $timelinetree;
}
?>