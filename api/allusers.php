<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of allusers
 *
 * @author laurens
 */
header('Content-type: application/json');
header("Content-Disposition: inline; filename=profile.json");


require_once('../includes.php');
require_once('../SMNIncludes.php');

if( !ini_get('safe_mode') ){
	set_time_limit(0);
        ini_set('memory_limit', '512M');
}

$existing_users = SMNUserQueries::allUsers();

echo json_encode($existing_users);
?>
