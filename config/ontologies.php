<?php
//VOCABULARIES
define("SIOC", "http://rdfs.org/sioc/ns#");
define("SIOCT", "http://rdfs.org/sioc/types#");
define("DCTERMS", "http://purl.org/dc/terms/");
define("DC","http://purl.org/dc/elements/1.1/source#");
define("FOAF", "http://xmlns.com/foaf/0.1/");
define("RDFS","http://www.w3.org/2000/01/rdf-schema#");
define("RDF", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
define("VCARD", "http://www.w3.org/2001/vcard-rdf/3.0/");
define("SKOS", "http://www.w3.org/2004/02/skos/core#");
define("POS","http://www.w3.org/2003/01/geo/wgs84_pos#");
define("GN","http://www.geonames.org/ontology#");
define("CTAG","http://commontag.org/ns#");
define("NT","http://ns.inria.fr/nicetag/2010/09/09/voc#");
define("OWL", "http://www.w3.org/2002/07/owl#");
define("DBP", "http://dbpedia.org/ontology/");
define("SWRC", "http://swrc.ontoware.org/ontology#");

//URIs
define("GRABTWEET", "http://grabeeter.tugraz.at/tweet/");
define("TIMELINE_URI","http://api.twitter.com/1/statuses/user_timeline.json?screen_name=");
define("TWITTER_URI","http://api.twitter.com/1/users/show.json?user_id=");
define("TWITTER_BASE","http://twitter.com/");
define("GEO_URI","http://ws.geonames.org/searchJSON?q=");
define("CALAIS","http://api.opencalais.com/enlighten/rest/");
define("TAGS_URI",URI."tags/");
define("PERSONS_URI",URI."persons/");


function fixPrefixes($line) {
        $prefixes = array();
	$original = array(SIOC,SIOCT,FOAF,DCTERMS,RDF,POS,GN,RDFS,CTAG,NT,DBP,SWRC,'"');
	$prefixes = array("sioc:","sioc_t:","foaf:","dct:","rdf:","wgs84_pos:","gn:","rdfs:",'ctag:','nt:','dbp:','swrc:','``');
	$newline = str_replace($original,$prefixes,$line);
	return $newline;
}

function isUri($str){
    if(!strpos($str,"///")&&!strpos($str,"/:")&&!(substr_count($str, ':')>1)) {
        $result = parse_url($str);
         if(array_key_exists('scheme',$result))
        	$isurl = $result['scheme']=='http'||$result['scheme']=="https" ? true : false;
        	else $isurl = false;
         $nospaces = (strpos($str,' ') ? false : true);
         return $isurl && $nospaces;
    } else return false;
}

function isFixed($str) {
	$nospaces = (strpos($str,' ') ? false : true);
	$nostripes = (strpos($str,'-') ? false : true);
	return (substr_count($str,':')==1 ? true : false) && $nospaces && $nostripes;
}
function isDateString($str) {
	return strpos($str, 'xsd:dateTime')!=false? true : false;
}
function formatTriple($triple) {
        $predicate = fixPrefixes($triple->getPredicate());
   	$desc = isUri($predicate) ? "<".$predicate.">" : (isFixed($predicate) ? ''.$predicate.'' : '"'.$predicate.'"');
   	$desc .= "\t\t";
   	if(isDateString($triple->getObject())) {
   		$desc .= $triple->getObject();
   	} else {
 	  	$object = fixPrefixes($triple->getObject());
   		$desc .= isUri($object) ? "<".$object.">": ((isFixed($object)) ? ''.$object.'' : '"'.$object.'"');
   	}
   	return $desc;
}
function rewriteTriple($triple) {
        $predicate = "<".$triple->getPredicate().">";
        $object = $triple->getObject();
   	if(isDateString($object)) {
   		$object = $triple->getObject();
   	} else {
   		$object = isUri($object) ? "<".$object.">": '"'.$object.'"';
   	}
        $triple->changePredicate($predicate);
        $triple->changeObject($object);

        return $triple;
}
?>