<?php

function getTwitterUsers() {
    $link = mysql_connect(GRAB_SOCKET, GRAB_USER, GRAB_PASS);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }

    if (!mysql_select_db(DB, $link)) {
        echo 'Could not select database';
        exit;
    }
    $screen_names = array();
    $i = 0;

    $sql = sprintf("SELECT screen_name FROM tweet_user WHERE 1");
    $result = mysql_query($sql, $link);
    while ($row = mysql_fetch_assoc($result)) {
        $screen_names[$i++] = $row['screen_name'];
    }
    mysql_free_result($result);
    mysql_close($link);
    return $screen_names;
}

function registerUser($username) {
    if (!USE_GRABEETER)
        loadUserFast($username);
    else {
        $grabeeterusers = getTwitterUsers();
        if (!in_array($username, $grabeeterusers))
            echo file_get_contents('http://grabeeter.tugraz.at/api/df0457d532bcc4fcb7c92cd11928972b1b6d6cb8/register/' . $username);
        else
            loadUserFast($username);
    }
    echo $username . " is now registered, your data should be available in a few hours.\n";
}

?>