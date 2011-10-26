<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('SMNIncludes.php');
require_once('includes.php');

error_reporting(E_ERROR | E_PARSE);

$existing_users = SMNUserQueries::allUsers();
$all_existing_tags = SMNTagQueries::allHashTags();
$existing_tags = array_chunk($all_existing_tags, 400, true);

$tagsnumber = count($all_existing_tags);
echo "Interlinking ".$tagsnumber." tags <br/>".PHP_EOL;

$elapsed_tags = 0;
$average_time = 0;
$remaining_est_time = 0;
$elapsed_time = 0;

foreach($existing_tags as $tags) {
        //echo "current tags: ".print_r($tags).PHP_EOL.PHP_EOL;
        list($usec, $sec) = explode(' ', microtime());
        $script_start = (float) $sec + (float) $usec;
        echo "found conferences: ".PHP_EOL;
        print_r(SMNOpenDataLinker::colindaMeanings($tags));
        echo PHP_EOL;
        list($usec, $sec) = explode(' ', microtime());
        $script_end = (float) $sec + (float) $usec;
        $elapsed_time += round($script_end - $script_start, 5);
        $elapsed_tags+=count($tags);
        $average_time = $elapsed_time / $elapsed_tags;
        $remaining_est_time = ($tagsnumber - $elapsed_tags) * $average_time;
        echo "Done: " . $elapsed_tags . "tags - Time: " . convertTime($elapsed_time) . " Est Remaining: " . convertTime($remaining_est_time) . '<br />' . PHP_EOL;
}

?>
