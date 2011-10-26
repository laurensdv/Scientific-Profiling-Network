<?php
require_once 'includes.php';
require_once 'SMNIncludes.php';

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

if (array_key_exists('user', $_GET)) {
    loadUserFast($_GET['user']);
    $user = $_GET['user'];
    $ourFileName = $user . ".json";
    $fh = fopen(dirname(__FILE__) . "/api/cached_discovery/" . $ourFileName, 'w');

    $results = SMNUserFinder::findRelatedUsers($user, null, null, null);
    $json_results = json_encode($results);

    fwrite($fh, $json_results);
    fclose($fh);
}
else {
$grabeteer_screen_names = getTwitterUsers();
$existing_users = SMNUserQueries::allUsers();
sort($grabeteer_screen_names);
sort($existing_users);
$new_users = array_diff($grabeteer_screen_names,$existing_users);
print_r($new_users);
loadGrabeeterUsers($new_users);

foreach ($new_users as $user) {
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
}
?>