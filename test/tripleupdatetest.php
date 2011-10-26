<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../SMNIncludes.php';

$source = '<http://api.twitter.com/1/users/show.json?user_id=204933726>';
$predicate = 'foaf:name';
$object = '"Sandrina Dens"';

SMNStore::updateTriple($source,$predicate,$object);
SMNStore::updateTriple($source,"dct:Location",'"Overijse, Belgium"');
?>
