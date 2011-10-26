<?php
function getFileWithParams($url,$params=null) {
	$ch = curl_init(urlencode($url)); 
	curl_setopt($ch, CURLOPT_NOBODY, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	if($params !=null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_POST, 1);
	}
	$result = curl_exec($ch); 
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	curl_close($ch);
	if($status_code == 200) return $result;
	else return false;
}
function getFile($url) {
	$ch = curl_init($url); 
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_HTTPGET, true); 
	$result = curl_exec($ch); 
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	curl_close($ch);
	return $result;
}

function getMultiHttp($urls) {
	$chs = array();
	$results = array();
	//create the multiple cURL handle
	$mh = curl_multi_init();
	
	// create both cURL resources
	// set URL and other appropriate options
        if($urls) {
	foreach($urls as $key => $url) {
		$chs[$key] = curl_init($url);
		curl_setopt($chs[$key], CURLOPT_TIMEOUT, 30);
		curl_setopt($chs[$key], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chs[$key], CURLOPT_HTTPGET, true);
		curl_multi_add_handle($mh,$chs[$key]);
	}

	$running=null;
	
	//execute the handles
	do {
		usleep(10000);
		$mhr = curl_multi_exec($mh,$running);
	} while ($running > 0);

	//get results and close the handles
	foreach($chs as $key => $ch) {
		$results[$key] = curl_multi_getcontent($ch);
		curl_multi_remove_handle($mh, $ch);
		curl_close($ch);
	}
	curl_multi_close($mh);
        } else $results = null;
	return $results;
}

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}