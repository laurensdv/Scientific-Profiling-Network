<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'includes.php';
require_once 'SMNIncludes.php';

$suggestion = null;
$loc = 'api/cached_discovery/';
$rem = 'http://el-devtc01.tugraz.at/~ldv/api/cached_discovery/';
$dir = $loc;
$handle = opendir($dir);

$twitter = new Twitter("JUZB6yp8EgrxBwoYzZiJSQ", "ucdm5aprKm8Y6b68GBGulVVTI1ADPQYBK84eDwU");

$twitter->setOAuthToken('306945484-SC72RyGrcbY5YWszVWVG8MbxaFeJagq0RKMAxw5T');
$twitter->setOAuthTokenSecret('IUmsvxmBe1AjxYP2Ilt1D8iSmYcwchYlIgIfzBDuO5o');

$twitter->statusesUpdate('Notifying users of new interesting users with whom they show many affinities.');

while (false !== ($file = readdir($handle))) {
    try {
        $extension = strtolower(substr(strrchr($file, '.'), 1));
        if ($extension == 'json') {
            $fh = fopen($dir . $file, 'r');
            $theData = "";
            while (!feof($fh)) {
                $theData .= fread($fh, 8192);
            }
            fclose($fh);

            $screen = substr($file, 0, -5);
            $screen_s = null;
            $theDataDec = object2array(json_decode($theData));

            if (array_key_exists('scientific_events', $theDataDec)) {
                $array = array_keys(object2array($theDataDec['scientific_events']));
                $uri = $array[1];
                $screen_s = derefUri($uri);
            } else {
                if (array_key_exists('tags', $theDataDec)) {
                $array = array_keys(object2array($theDataDec['tags']));
                $uri = $array[1];
                $screen_s = derefUri($uri);
                } else {
                $array = array_keys($theDataDec['mentions']);
                $uri = $array[1];
                $screen_s = derefUri($uri);
                }
            }

            $textfile ="random.txt";
            $items = file("$textfile");
            $item = rand(0, sizeof($items)-1);
            
            echo $items[$item];
            
            if ($screen_s != null) {
                $text = "@".$screen.", today's analysis of yr tweets in http://bit.ly/b5LO7G suggests @".$screen_s." 2 u, find more at http://bit.ly/resafb";
                echo PHP_EOL.$text.'<br />';
                $twitter->statusesUpdate($text);
                $time = rand(15,45);
                echo PHP_EOL."sleeping: ".$time.'<br />';
                sleep($time);
            }
        }
    } catch (Exception $e) {
        $e->printStackTrace();
    }
}

function derefUri($uri) {
    $file = file_get_contents("http://api.semanticprofiling.net/profile.php?uri=" . $uri);
    $data = object2array(json_decode($file));
    return $data['screen_name'];
}

?>
