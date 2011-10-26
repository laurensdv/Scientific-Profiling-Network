<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../SMNIncludes.php';

if ($_GET != null) {
    $uri = SMNUserQueries::personUri($_GET['q']);
    if(!$uri) {
        $screen = SMNUserQueries::userScreenName($_GET['q']);
        $uri = SMNUserQueries::personUri($screen);
    }
    $parser = ARC2::getRDFParser();
    $ser = ARC2::getRDFXMLSerializer();
    $profile = SMNUserQueries::profile($uri);

    $doc = $parser->toRDFXML($profile);
    //header("Content-Type: application/rdf+xml");
    print_r($doc);
} else echo "Not found";


?>
