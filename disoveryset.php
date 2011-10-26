<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!ini_get('safe_mode')) {
    set_time_limit(0);
    ini_set('memory_limit', '512M');
}

require_once('includes.php');
require_once('SMNIncludes.php');

$existing_users = SMNUserQueries::allUsers();

$number = count($existing_users);
echo "Caching " . $number . " user discoveries <br/>" . PHP_EOL;

$elapsed = 0;
$average_time = 0;
$remaining_est_time = 0;
$elapsed_time = 0;

foreach ($existing_users as $user) {
    list($usec, $sec) = explode(' ', microtime());
    $script_start = (float) $sec + (float) $usec;
    $ourFileName = $user . ".json";
    $fh = fopen(dirname(__FILE__) . "/api/cached_discovery/" . $ourFileName, 'w');

    $results = SMNUserFinder::findRelatedUsers($user, null, null, null);
    $json_results = json_encode($results);

    fwrite($fh, $json_results);
    fclose($fh);
    list($usec, $sec) = explode(' ', microtime());
    $script_end = (float) $sec + (float) $usec;
    $elapsed_time += round($script_end - $script_start, 5);
    $elapsed+=1;
    $average_time = $elapsed_time / $elapsed;
    $remaining_est_time = ($number - $elapsed) * $average_time;
    echo "Done: " . $elapsed . " users - Time: " . convertTime($elapsed_time) . " Est Remaining: " . convertTime($remaining_est_time) . '<br />' . PHP_EOL;
}
?>
