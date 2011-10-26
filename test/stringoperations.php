<?php
require_once "../config/ontologies.php";
require_once "../extraction/microblogaccount/taggers/contenttagger.php";
require_once "model/queryhandler.php";

$urls[]='http://api.tagdef.com/'.'fb'.'.json';
$urls[]='http://www.cnn.com/';
$results=getMultiHttp($urls);
print_r($results);

$format = 'Y-m-d H:i:s';
$date = new DateTime('2010-08-20 15:01:14');
echo "Format: $format; " . $date->format('Y-m-d\TH:i:sP') . "^^xsd:dateTime";

//$ht=file_get_contents('http://api.tagdef.com/'.'fkljlklb'.'.json');
$htall=json_decode($ht,true);
print_r($htall);



$ch = curl_init('http://api.tagdef.com/'.'fb'.'.json'); 
curl_setopt($ch, CURLOPT_NOBODY, true); 
curl_exec($ch); 
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
curl_close($ch);
echo $status_code;

$object="sfkjlsdf http://www.test.com";
$str="http://dsfjlsdfj.dfkjsl.domm kfjlsdfjqmfjm";

$pos = strpos($object,'http://');
if($pos==false) $pos = strpos($object,'://')+3;;
$object = substr($object, $pos);
$string = "http://someting.com%&Some more text";
print_r(explode('%&',$string));
echo $string.PHP_EOL;
echo $object.PHP_EOL;

print_r(parse_url($str));

$userdescription='Semantic Web, Linked Data, Social Media, Reputation Systems enthusiast and researcher. Phd Student @ Graz University of Technology & a very bad guitar player ;)';

$tagsraw = calaisQuery($userdescription);

//echo $tagsraw;

//print_r($tagsraw);

$tagall=json_decode($tagsraw,true);

//print_r($tagall);

echo "getFile".PHP_EOL;

$dbp=getFile('http://dbpedia.org/data/Poland.jsod');

echo $dbp;

$tagall=json_decode($dbp,true);

print_r($tagall);

//print_r(lookUpTagMeaning('poland'));

echo $tagall['d']['__count'];

print_r(createAutoTag('Poland'));

print_r(createHashTag('Poland'));

print_r(createEntityTag('http://uri.com/country%&country%&Poland'));
?>