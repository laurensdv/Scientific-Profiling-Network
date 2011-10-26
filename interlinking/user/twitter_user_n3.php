<?php
require_once '../../includes.php';

if( !ini_get('safe_mode') ){ 
	set_time_limit(120); 
} 

$user=$_GET['user'];

//Annotate the user data
$usertrees = annotateUser($user);

//Build the document

//DOCUMENT HEADER
echo "# This document is generated on Semantic Profiling Network.\n";
echo "# It is the N3 version of twitter user: ".$user." and its tweets grabbed from Grabeeter.\n";
echo "\n\n";
echo "@prefix sioc: <".SIOC.">\n";
echo "@prefix sioc_t: <".SIOCT.">\n";
echo "@prefix foaf: <".FOAF.">\n";
echo "@prefix dc: <".DCTERMS.">\n";
echo "@prefix rdfs: <".RDFS.">\n";
echo "@prefix rdf: <".RDF.">\n";
echo "@prefix wsg84_pos: <".POS.">\n";
echo "@prefix gn: <".GN.">\n";
echo "@prefix ctag: <".CTAG.">\n";
echo "@prefix nt: <".NT.">\n";
echo PHP_EOL;


//PROFILE
$useraccount = $usertrees['user']->getRoot();
$usertriples = userTriples($usertrees);
echo "<".$useraccount.">\n";
$i=0;
foreach($usertriples as $triple) {
	$line = "\t".formatTriple($triple);
	echo $line;
	if(++$i==count($usertriples)) echo " .\n";
	else echo ";\n";
}
echo PHP_EOL;

//TIMELINE
$usertimeline = $usertrees['timeline']->getRoot();
$timelinetriples = timeLineTriples($usertrees);
echo "<".$usertimeline.">\n";
$i=0;
foreach($timelinetriples as $triple) {
	$line = "\t".formatTriple($triple);
	echo $line;
	if(++$i==count($timelinetriples)) echo " .\n";
	else echo ";\n";
}
echo PHP_EOL;

//TWEETS

foreach($usertrees['tweets'] as $tweettree) {
	$tweettriples = tweetTriples($tweettree);
	echo '<'.$tweettree->getRoot().'>'.PHP_EOL;
	$i=0;
	foreach($tweettriples as $triple) {
		$line = "\t".formatTriple($triple);
		echo $line;
		if(++$i==count($tweettriples)) echo " .\n";
		else echo ";\n";
	}
	echo PHP_EOL;
}

echo PHP_EOL;

//TAGS
if($usertrees['tags']!=null) {
	foreach($usertrees['tags'] as $tagtree) {
		$tagtriples = tagTriples($tagtree);
		echo '<'.$tagtree->getRoot().'>'.PHP_EOL;
		$i=0;
		foreach($tagtriples as $triple) {
			$line = "\t".formatTriple($triple);
			echo $line;
			if(++$i==count($tagtriples)) echo " .\n";
			else echo ";\n";
		}
	echo PHP_EOL;
	}
}

echo PHP_EOL;

//GEOLOCATIONS

if($usertrees['geolocations']!=null) {
	foreach($usertrees['geolocations'] as $geotree) {
		$geotriples = geoTriples($geotree);
		echo '<'.$geotree->getRoot().'>'.PHP_EOL;
		$i=0;
		foreach($geotriples as $triple) {
			$line = "\t".formatTriple($triple);
			echo $line;
			if(++$i==count($geotriples)) echo " .\n";
			else echo ";\n";
		}
	echo PHP_EOL;
	}
}
?>