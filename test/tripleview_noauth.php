<?php
require_once '../view/show_array.php';
require_once '../extraction/microblogaccount/userprofilemodel.php';

$user=$_GET['user'];
$useraccount = 'http://twitter.com/'.$user;
$usertree=createUserTripleTree($user);

echo '<div><strong>'.$useraccount.'</strong><br /><br />';
html_show_array($usertree->getProperties());
echo '</div>';

?>