<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../analysis/PorterStemmer.php');
require_once ('../analysis/includes.php');

echo PorterStemmer::Stem("blogger");
echo PorterStemmer::Stem("blogs");
echo PorterStemmer::Stem("blogging");

$command = '/usr/local/WordNet-3.0/bin/wn age -synsn';
$output = array();
exec($command, $output);
print_r($output);

//print_r(SMNTagFilter::syntacticFilter(array("blogger","Zürich","Gröbming")));
print_r(SMNTagFilter::filter(array("blogs","zurrich","Zürich","Gröbming")));
?>
