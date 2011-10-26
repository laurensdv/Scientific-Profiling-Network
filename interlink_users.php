<?php
require_once('SMNIncludes.php');
require_once('includes.php');

$existing_users = SMNUserQueries::allUsers();

echo "Interlinking users <br/>".PHP_EOL;

foreach($existing_users as $existing_user) {
    echo "current user: ".$existing_user.PHP_EOL;
    try{
        SMNOpenDataLinker::interlinkUser($existing_user);
    } catch(TwitterException $e) {
        echo "Error: ".$e->getMessage().PHP_EOL.$e->getTraceAsString().PHP_EOL;
    }
}

?>
