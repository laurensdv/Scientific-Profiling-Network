<?php
function loadGrabeeterUsers($screen_names) {
    ob_start();
    $usernumber = count($screen_names);
    $elapsed_users = 0;
    $average_time = 0;
    $remaining_est_time = 0;
    $elapsed_time = 0;
    echo "Found: " . $usernumber . "users <br />" . PHP_EOL;
    ob_flush();
    flush();
    foreach ($screen_names as $screen_name) {
        //Start time
        list($usec, $sec) = explode(' ', microtime());
        $script_start = (float) $sec + (float) $usec;
        //Do activity
        try {
            loadUserFast($screen_name);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            echo "Error: " . $e->getMessage() . "<br />";
            ob_flush();
            flush();
        }
        //Stop time
        list($usec, $sec) = explode(' ', microtime());
        $script_end = (float) $sec + (float) $usec;
        //Update time
        $elapsed_time += round($script_end - $script_start, 5);
        $elapsed_users++;
        $average_time = $elapsed_time / $elapsed_users;
        $remaining_est_time = ($usernumber - $elapsed_users) * $average_time;
        echo "Done: " . $elapsed_users . " Time: " . convertTime($elapsed_time) . " Est Remaining: " . convertTime($remaining_est_time) . '<br />' . PHP_EOL;
        ob_end_flush();
        ob_flush();
    }
}

function loadUserFast($screen_name) {
    //Start time
    ob_start();
    list($usec, $sec) = explode(' ', microtime());
    $script_start = (float) $sec + (float) $usec;
    $parser = ARC2::getTurtleParser();
    $base = ROOT;
    $data = array();
    echo 'Loading: ' . $screen_name . "<br />" . PHP_EOL;
    $user = new UserTurtler($screen_name, true, false);
    ob_flush();
    flush();
    $user->store();
    //Stop time
    list($usec, $sec) = explode(' ', microtime());
    $script_end = (float) $sec + (float) $usec;
    $elapsed_time = round($script_end - $script_start, 5);
    echo $screen_name . " execution time: " . convertTime($elapsed_time) . "<br />" . PHP_EOL;

    ob_flush();
    flush();

    //SMNStore::doPing($screen_name.'s Twitter Profile',ROOT.'/'.$screen_name);
}

function updateUser($screen_name) {
    //Start time
    ob_start();
    list($usec, $sec) = explode(' ', microtime());
    $script_start = (float) $sec + (float) $usec;
    $parser = ARC2::getTurtleParser();
    $base = ROOT;
    $data = array();
    echo 'Loading: ' . $screen_name . "<br />" . PHP_EOL;
    $result = SMNUserQueries::latestTweetDate($screen_name);
    $date = new DateTime($result);
    $user = new UserTurtler($screen_name, true, false, $date->format('Y-m-d\TH:i:s'));
    ob_flush();
    flush();
    $user->store();
    //Stop time
    list($usec, $sec) = explode(' ', microtime());
    $script_end = (float) $sec + (float) $usec;
    $elapsed_time = round($script_end - $script_start, 5);
    echo $screen_name . " execution time: " . convertTime($elapsed_time) . "<br />" . PHP_EOL;

    ob_flush();
    flush();

    //SMNStore::doPing($screen_name.'s Twitter Profile',ROOT.'/'.$screen_name);
}

function loadUser($screen_name) {
    //Start time
    ob_start();
    list($usec, $sec) = explode(' ', microtime());
    $script_start = (float) $sec + (float) $usec;
    $parser = ARC2::getTurtleParser();
    $base = ROOT;
    $data = array();
    echo 'Loading: ' . $screen_name . "<br />" . PHP_EOL;
    $user = new UserTurtler($screen_name, true, false);
    $data['user'] = $user->getUserProfile();
    $data['timeline'] = $user->getUserTimeline();
    $data['tags'] = $user->getAllTags();
    $data['locations'] = $user->getAllGeoLocations();

    foreach ($user->getAllTweets() as $key => $value) {
        $data[$key] = $value;
    }
    ob_flush();
    flush();

    foreach ($data as $key => $value) {
        $store = setUpStore();
        $parser = ARC2::getTurtleParser();
        $parser->parse($base, $value);
        $triples = $parser->getTriples();
        $index = $parser->getSimpleIndex(0);


        //Manageable chunks to load the database
        $indices = array();
        if (LOADSIZE < (count(reset($index)) + 2)) {
            while (current($index) != end($index)) {
                $chunks = array_chunk(current($index), LOADSIZE, true);
                $j = 0;
                foreach ($chunks as $chunk) {
                    $indices[$j++][key(current($index))] = $chunk;
                }
                next($index);
            }
            $chunks = array_chunk(end($index), LOADSIZE, true);
            $j = 0;
            foreach ($chunks as $chunk) {
                $indices[$j++][key(current($index))] = $chunk;
            }
        } else {
            if (count($index) > LOADSIZE / (count(end($index)) + 2)) {
                $indices = array_chunk($index, LOADSIZE / (count(end($index)) + 2), true);
            } else
                $indices[] = $index;
        }
        $buffers = $indices;

//        foreach ($indices as $index) {
//            foreach ($index as $subject => $properties) {
//                if (count($properties) > LOADSIZE/count($index)) {
//                    $buffers[$root] = array_chunk($subject, LOADSIZE, true);
//                } else
//                    $buffers[] = $index;
//            }
//          }
        //Load the buffers in to the database in chunks of LOADSIZE
        //echo "START inserting " . $key . " -data <br />" . PHP_EOL;
        foreach ($buffers as $buffer) {
            if ($buffer != null)
                $rs = $store->insert($buffer, $base, 0);
            //$rs = $store->query("INSERT INTO <".$base."> {".$value."}");
            if ($errors = $store->getErrors()) {
                error_log("SPARQL Error:\n" . join("\n", $errors));
                echo "BUFFER: " . key($buffer) . "\n <br />";
                echo "ERROR: " . join("\n", $errors) . "<br />";
            }
            //echo '     ';
            //print_r($rs);
            //echo '<br />';
        }
        //echo "DONE inserting " . $key . "-data <br />" . PHP_EOL;
        ob_flush();
        flush();
    }

    //Stop time
    list($usec, $sec) = explode(' ', microtime());
    $script_end = (float) $sec + (float) $usec;
    $elapsed_time = round($script_end - $script_start, 5);
    echo $screen_name . " execution time: " . convertTime($elapsed_time) . "<br />" . PHP_EOL;

    ob_flush();
    flush();

    //print_r($triples);
    //$doc = $parser->toRDFXML($index);
    //print_r($doc);
    //$myFile = $screen_name.".rdf";
    //$fh = fopen("../".$myFile, 'w') or die("can't open file");
    //fwrite($fh, $doc);
    //fclose($fh);
    //SMNStore::doPing($screen_name.'s Twitter Profile',ROOT.'/'.$screen_name);
}

function loadRDF($rdfurl) {
    $parser = ARC2::getParser();
    $base = '';
    $store = setUpStore();
    $value = file_get_contents($rdfurl);
    $parser->parse($base, $value);
    $triples = $parser->getTriples();
    $index = $parser->getSimpleIndex(0);

    if ($index != null) {
        $rs = $store->insert($index, $base, 0);
        $errors = $store->getErrors();
        if ($errors) {
            error_log("SPARQL Error:\n" . join("\n", $errors));
        }
    }
    return $rs;
}

function convertTime($time) {
    $diff = $time;
    $daysDiff = floor($diff / 60 / 60 / 24);
    $diff -= $daysDiff * 60 * 60 * 24;
    $hrsDiff = floor($diff / 60 / 60);
    $diff -= $hrsDiff * 60 * 60;
    $minsDiff = floor($diff / 60);
    $diff -= $minsDiff * 60;
    $secsDiff = floor($diff);
    $diff-=$secsDiff;
    $msDiff = round($diff * 1000);
    return ($daysDiff . 'd ' . $hrsDiff . 'h ' . $minsDiff . 'm ' . $secsDiff . 's ' . $msDiff . 'ms)');
}

function setUpStore() {
    /* instantiation */
    $config_arc = getConfigArc();
    $store = ARC2::getStore($config_arc);

    if (!$store->isSetUp()) {
        $store->setUp();
    }
    return $store;
}

function sizeAbleIndices($index) {

}

?>
