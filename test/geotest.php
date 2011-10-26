<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../includes.php');
require_once('../SMNIncludes.php');
require_once('../interlinking/SMNUserQueries.php');
require_once('../interlinking/SMNStore.php');
require_once('../interlinking/SMNLocation.php');


$allusers = SMNUserQueries::allUsers();
$locations = array();

foreach($allusers as $user) {
    $uri = SMNUserQueries::userUri($user);
    $locations[$user] = SMNUserQueries::location($uri);
}

html_show_array($locations);

html_show_array(SMNLocation::describe('http://ws.geonames.org/searchJSON?q=Gent,Europe'));

echo SMNLocation::latitude('http://ws.geonames.org/searchJSON?q=Gent,Europe').PHP_EOL.PHP_EOL;
echo SMNLocation::longitude('http://ws.geonames.org/searchJSON?q=Gent,Europe').PHP_EOL.PHP_EOL;

echo "Users within 1500 km".PHP_EOL.PHP_EOL;
html_show_array(SMNUserFinder::filterUsersByLocationOfUser(SMNUserQueries::userUri('amersch'),1500));
echo "Users withi 100km".PHP_EOL.PHP_EOL;
html_show_array(SMNUserFinder::filterUsersByLocationOfUser(SMNUserQueries::userUri('amersch'),100));
echo "Most popular users".PHP_EOL.PHP_EOL;
$trimmed_friends = trim_array(array_keys(SMNUserFinder::mostPopularUsersFast()), 100);
html_show_array($trimmed_friends);
//html_show_array(array_keys(SMNUserFinder::mostPopularUsersFast()));

?>
