<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../analysis/SMNLsi.php';


if( !ini_get('safe_mode') ){
	set_time_limit(120);
}


$smnlsi = new SMNLsi();
$user = $_GET['user'];
print_r($smnlsi->getTFIDF($user));
?>
