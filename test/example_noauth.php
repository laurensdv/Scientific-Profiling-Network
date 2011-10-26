<?php
// require
require_once '../extraction/microblogaccount/twitter.php';
require_once '../view/show_array.php';

// create instance
$twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

// set tokens
$twitter->setOAuthToken('77273706-2NRkKzbIpxutFWCRG7hLfaDZEywSkGbcRsxl69Zqg');
$twitter->setOAuthTokenSecret('2PZXbP8ZMlAtu3LAcXUlzuZpj17V0oufI4mxHWSY');

$userdata = $twitter->usersShow($_GET['user']);

echo "<div>";
echo "<strong>Resulting user profile data from Twitter</strong><br><br>";
echo "<em>Short description</em><br>";
echo "<br> <em>Screen name:</em> ".$userdata["screen_name"];
echo "<br> <em>Last status update:</em> ".$userdata["status"]["text"];
echo "<br> <em>Location:</em> ".$userdata["location"];
echo "<br> <em>Description:</em> ".$userdata["description"];
echo "<br></div>";

echo "<div>";
echo "<br><em>Image</em><br>";
echo '<br><img src="'.$userdata["profile_image_url"].'" alt="Profile Image"></img>';
echo "<br></div>";

echo "<div>";
echo "<br><em>Full profile</em><br><br>";
html_show_array($userdata);
echo "</div>";

?>