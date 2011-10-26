<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../SMNIncludes.php';


if ($_GET != null) {
    $parser = ARC2::getRDFParser();
    $ser = ARC2::getRDFXMLSerializer();
    $uri = SMNUserQueries::userUri($_GET['q']);
    $profile = SMNUserQueries::profile($uri);

    $doc = $parser->toRDFXML($profile);
    //header("Content-Type: application/rdf+xml");
    print_r($doc);
} else echo "URI not found";
?>
