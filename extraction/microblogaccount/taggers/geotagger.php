<?php
function geoTag($tweets) {
	$geoTripleTrees = array();
	foreach($tweets as $tweet) {
		$tweetprops = $tweet->getProperties();
		if(array_key_exists(POS.'lat',$tweetprops)&&array_key_exists(POS.'long',$tweetprops)) {
			$latitude= $tweetprops[POS.'lat'];
			$longitude = $tweetprops[POS.'long'];
			$geo_url="http://ws.geonames.org/findNearbyJSON?lat=".$latitude."&lng=".$longitude;
			//$georaw=file_get_contents($geo_url,true,1);
			//$geoall=json_decode($georaw,true);
			//$geoinfo=$geoall['geonames'][0];
			$geoTripleTree=new TripleTree($geo_url);
			$geoTripleTree->addProperty(RDF.'type',POS.'Point');
			//$geoTripleTree->addProperty(RDFS.'SeeAlso', "http://sws.geonames.org/".$geoinfo['geonameId']."/about.rdf");
			//$geoTripleTree->addProperty(RDFS.'link', "http://www.geonames.org/".$geoinfo['geonameId']);
			$geoTripleTree->addProperty(RDFS.'label',$tweetprops[DCTERMS.'spatial']);
			$geoTripleTree->addProperty(GN.'countryCode',$tweetprops[GN.'countryCode']);
                        $geoTripleTree->addProperty(GN.'country',$tweetprops[GN.'country']);
			$geoTripleTree->addProperty(POS.'long', $longitude);
			$geoTripleTree->addProperty(POS.'lat', $latitude);
                        $tweet->addProperty(DCTERMS.'spatial',$geoTripleTree->getRoot());
			$index=$latitude.",".$longitude;
			$geoTripleTrees[$index]=$geoTripleTree;
		}
                //if(array_key_exists(DCTERMS . 'Location',$tweetprops)) {
                //        $geoTripleTrees[]=geoAnnotate($tweetprops[DCTERMS.'Location'].','.$tweetprops[GN.'countryCode']);
                //}
			
	}
	return $geoTripleTrees;
}

function geoAnnotate($loc_string) {
	$geoTripleTree = null;
	if(strlen($loc_string)==3) $loc_string = convertCountryCode($loc_string);
	$geo_url=GEO_URI.$loc_string;
	$geo_url = preg_replace('/\s*/m' , '' , $geo_url);
	$georaw=file_get_contents($geo_url."&maxRows=1&style=short");
	$geoall=json_decode($georaw,true);
        if(!is_array($geoall)||$geoall==null) return null;
	if($geoall['totalResultsCount']>0) {
		$geoinfo=$geoall['geonames'][0];
		$geoTripleTree=new TripleTree($geo_url);
		$geoTripleTree->addProperty(RDF.'type',POS.'Point');
		$geoTripleTree->addProperty(RDFS.'seeAlso', "http://sws.geonames.org/".$geoinfo['geonameId']."/about.rdf");
		$geoTripleTree->addProperty(RDFS.'link', "http://www.geonames.org/".$geoinfo['geonameId']);
		$geoTripleTree->addProperty(RDFS.'label',$geoinfo['toponymName']);
		$geoTripleTree->addProperty(GN.'countryCode',$geoinfo['countryCode']);			
		$geoTripleTree->addProperty(POS.'long', $geoinfo['lng']);
		$geoTripleTree->addProperty(POS.'lat', $geoinfo['lat']);
	}
	return $geoTripleTree;
}

function convertCountryCode($iso3) {
	$countries = readCountriesCSV();
	$country = $countries[$iso3];
	if($country) $iso3 = $country[4];
	return $iso3;
}
?>