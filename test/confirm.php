<?php
echo "Callback Page";
// require
require_once '../extraction/microblogaccount/twitter.php';

// create instance
$twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

// authorize
if(!isset($_GET['oauth_token'])) $twitter->oAuthAuthorize();

$userdata = $twitter->usersShow('laurens_d_v');

print_r($userdata);
?>