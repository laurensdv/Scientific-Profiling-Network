<?php
// require
require_once '../extraction/microblogaccount/twitter.php';

// create instance
$twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

// get a request token
$twitter->oAuthRequestToken('oob');

// authorize
if(!isset($_GET['oauth_token'])) $twitter->oAuthAuthorize();

// get tokens
$response = $twitter->oAuthAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);

// output, you can use the token for setOAuthToken and setOAuthTokenSecret
var_dump($response);

$userdata = $twitter->usersShow('laurens_d_v');

print_r($userdata);


?>