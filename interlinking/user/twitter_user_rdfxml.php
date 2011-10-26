<?php
require_once '../../includes.php';

if( !ini_get('safe_mode') ){ 
	set_time_limit(300); 
} 



$user=$_GET['user'];
$url="twitter_user_n3.php";

$parser = ARC2::getRDFParser();
$data = get_include_contents($url);
$base = "http://linkeddata.semanticprofiling.net/";
$parser->parse($base,$data);
$index = $parser->getSimpleIndex(0);
$triples = $parser->getTriples();
$doc = $parser->toRDFXML($index);

header("Content-Type: Application/RDF+XML");
print_r($doc);
?>