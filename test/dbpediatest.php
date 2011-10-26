<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../includes.php");
require_once("../SMNIncludes.php");
$tags=array();
$tags[]='Twitter';
$tags[]='Michael Jackson';
SMNOpenDataLinker::dbPediaMeanings($tags);
SMNOpenDataLinker::interlinkTags($tags);
print_r(SMNTagQueries::isEntity('Michael Jackson'));
print_r(SMNTagQueries::isEntity('Michael_Jackson'));
?>
