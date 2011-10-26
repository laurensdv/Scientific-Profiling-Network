<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../SMNIncludes.php');
require_once('../includes.php');

if( !ini_get('safe_mode') ){
	set_time_limit(180);
}

if (array_key_exists('user', $_GET)) {
    try{
        $username = $_GET['user'];
        $allusers = SMNUserQueries::allUsers();
        if(!in_array($username, SMNUserQueries::allUsers())||$allusers=null||!is_array($allusers)) {
            registerUser($username);
        } else
            echo $username." is already registered";
    } catch(Exception $e) {
        echo "ERROR: ".$_GET['user']." - ".$e->getMessage();
        registerUser($_GET['user']);
    }
} else echo "ERROR: Please specify a screen name as 'user' Parameter.";
?>
