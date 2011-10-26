<html><body>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 *
 */
echo "<emph>Evaluation form submitted. </emph> <br /><br />";

//echo print_r($_GET).PHP_EOL;

$fh = fopen(dirname(__FILE__) . "/evaluation_results/" . time() . $_GET['name'] . '.json', 'w');

$json_results = json_encode($_GET);

fwrite($fh, $json_results);
fclose($fh);

echo 'User: '.$_GET['user'].'<br />';
echo 'Name: '.$_GET['name'].'<br />';
echo 'Email: '.$_GET['email'].'<br />';
echo 'Company: '.$_GET['company'].'<br />';
echo 'Location: '.$_GET['location'].'<br />';

if(!array_key_exists('nonRelCount', $_GET) || !array_key_exists('relCount', $_GET) || !array_key_exists('q0', $_GET) || !array_key_exists('comments', $_GET)) {
    echo "Not all data received succesfully, please resubmit.<br />";
} else echo "<br/><emph>All data received OK.</emph>";

?>
</body></html>
