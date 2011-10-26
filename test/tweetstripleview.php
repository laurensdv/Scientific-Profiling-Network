<?php
require_once '../view/show_array.php';
require_once '../extraction/microblogaccount/usertweetmodel.php';

$user=$_GET['user'];
$useraccount = 'http://twitter.com/'.$user;
$tweettree=createTweetsTripleTree($user);

echo '<div><strong>'.$useraccount.'</strong><br /><br />';
foreach ($tweettree as $tweet) {
	html_show_array($tweet->getProperties());
}
echo '</div>';
?>