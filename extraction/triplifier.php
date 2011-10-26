<?php
//retired function
function triplify($user) {
	$triples = array();
	$usertrees = annotateUser($user);
	$i = 0;
	
	foreach (toTriples($usertrees['timeline']) as $triple) {
		$triples[$i++]=$triple;
		echo $triple;
	}
	
	foreach (toTriples($usertrees['user']) as $triple) {
		$triples[$i++]=$triple;
		echo $triple;
	}
	
	foreach($usertrees['tweets'] as $tweettree) {
		foreach (toTriples($tweettree) as $triple) {
		$triples[$i++]=$triple;
		echo $triple;
		}
	}	
	
	foreach (toTriples($usertrees['geolocations']) as $geolocationtree) {
		foreach (toTriples($geolocationtree) as $triple) {
		$triples[$i++]=$triple;
		echo $triple;
	}
	}
	
	foreach (toTriples($usertrees['tags']) as $tagtree) {
		foreach (toTriples($tagtree) as $triple) {
		$triples[$i++]=$triple;
		echo $triple;
	}
	}
}

function timeLineTriples($usertrees) {
	$triples = array();
	$i=0;
	foreach (toTriples($usertrees['timeline']) as $triple) {
		$triples[$i++]=$triple;
	}
	return $triples;
}

function userTriples($usertrees) {
	$triples = array();
	$i=0;
	foreach (toTriples($usertrees['user']) as $triple) {
		$triples[$i++]=$triple;
	}
	return $triples;
}

function geoTriples($geotree) {
	$triples = array();
	$i=0;
	foreach (toTriples($geotree) as $triple) {
		$triples[$i++]=$triple;
	}
	return $triples;
}

function tagTriples($tagtree) {
	$triples = array();
	$i=0;
	foreach (toTriples($tagtree) as $triple) {
		$triples[$i++]=$triple;
	}
	return $triples;
}

function tweetTriples($tweettree) {
	$triples = array();
	$i=0;
	foreach (toTriples($tweettree) as $triple) {
		$triples[$i++]=$triple;
	}
	return $triples;
}

function toTriples($tripletree) {
        if($tripletree!=null) {
	$triples = array();
	$i=0;
	foreach($tripletree->getProperties() as $key => $value) {
		if(!is_array($value)) {
			$triples[$i++]=new Triple($tripletree->getRoot(),$key,$value);
		} else {
			foreach($value as $value_deep) {
				$triples[$i++]=new Triple($tripletree->getRoot(),$key,$value_deep);
			}
		}
	}
	return $triples;
        }
        return null;
}
?>