<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../SMNIncludes.php';

if ($_GET != null) {
    $parser = ARC2::getRDFParser();
    $uri = SMNTagQueries::tagUri($_GET['q']);
    $ser = ARC2::getRDFXMLSerializer();
    $detail = SMNTagQueries::tagDetails($uri);
    $doc = $parser->toRDFXML($detail);
    //header("Content-Type: application/rdf+xml");
    print_r($doc);
} else echo "Not found";
?>
