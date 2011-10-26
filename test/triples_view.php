<?php
require_once '../extraction/microblogaccount/show_array.php';
require_once '../extraction/microblogaccount/triplifier.php';

$user=$_GET['user'];
$useraccount = 'http://twitter.com/'.$user;

echo '<div><strong>'.$useraccount.'</strong><br /><br />';
$triples = triplify($user);
echo '</div>';
?>