<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../SMNIncludes.php";
require_once "../includes.php";

if(!array_key_exists('q', $_GET)) $conference = 'LREC2008';
     else $conference = $_GET['q'];

if(SMNConference::isConference($conference))
    print_r(SMNConference::findConference($conference));
else echo "Tag is not a conference";

$conferences = array(0 => "LREC08",1 => "ESWC11");

$results = SMNConference::findConferencesColinda($conferences);

foreach($results as $result) {
    $decoded_result = json_decode($result);
    print_r($decoded_result);
      foreach($decoded_result as $uri => $value) {
         echo "URI: ".$uri.PHP_EOL;
    }
}
?>
