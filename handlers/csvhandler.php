<?php
function printCSV($csv) {
	$row = 1;
	if (($handle = fopen($csv, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			$num = count($data);
			echo "<p> $num fields in line $row: <br /></p>\n";
			$row++;
			for ($c=0; $c < $num; $c++) {
				echo $data[$c] . "<br />\n";
			}
		}
		fclose($handle);
	}
	
}

function readCountriesCSV() {
	$csv = $path = realpath(dirname(__FILE__)) .'/'.'countryInfo.csv';
	$row = 1;
	$alldata = array();
	if (($handle = fopen($csv, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			$alldata[$data[1]]=$data;
		}
		fclose($handle);
	}
	return $alldata;
}
?>