<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'includes.php';
require_once 'SMNIncludes.php';

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

$existing_users = SMNUserQueries::allUsers();
foreach($existing_users as $user) {
    try{
        updateUser($user);
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}
?>
