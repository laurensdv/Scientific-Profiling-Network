<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../includes.php');
require_once('../SMNIncludes.php');

$result = SMNUserQueries::latestTweetDate($_GET['user']);
$date = new DateTime($result);
$date->format('Y-m-d\TH:i:sP');
print_r($date);
?>
