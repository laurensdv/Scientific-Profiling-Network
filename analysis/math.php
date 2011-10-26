<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function cosineSimilarity($tokensA, $tokensB)
{
    $a = $b = $c = 0;
    $uniqueTokensA = $uniqueTokensB = array();

    $uniqueMergedTokens = array_unique(array_merge($tokensA, $tokensB));

    foreach ($tokensA as $token) $uniqueTokensA[$token] = 0;
    foreach ($tokensB as $token) $uniqueTokensB[$token] = 0;

    foreach ($uniqueMergedTokens as $token) {
        $x = isset($uniqueTokensA[$token]) ? 1 : 0;
        $y = isset($uniqueTokensB[$token]) ? 1 : 0;
        $a += $x * $y;
        $b += $x;
        $c += $y;
    }
    return $b * $c != 0 ? $a / sqrt($b * $c) : 0;
}

function cosineSimilarity2($tokensA, $tokensB)
{
    $a = $b = $c = 0;
    $countTokensA = $countTokensB = array();

    $countMergedTokens = array_count_values(array_merge($tokensA, $tokensB));

    $countTokensA = array_count_values($tokensA);
    $countTokensB = array_count_values($tokensB);

    foreach ($countMergedTokens as $token => $count) {
        $x = ($countTokensA[$token]*2.0)/$count;
        $y = ($countTokensB[$token]*2.0)/$count;
        $a += $x * $y;
        $b += $x;
        $c += $y;
    }
    return $b * $c != 0 ? $a / sqrt($b * $c) : 0;
}

function cardinality($tokens) {
    return count($tokens);
}

function mysqlHaversine($lat = 0, $lon = 0, $distance = 0)
{
    if($distance > 0)
    {
        return ('
        ((6372.797 * (2 *
        ATAN2(
            SQRT(
                SIN(('.($lat*1).' * (PI()/180)-latitude*(PI()/180))/2) *
                SIN(('.($lat*1).' * (PI()/180)-latitude*(PI()/180))/2) +
                COS(latitude * (PI()/180)) *
                COS('.($lat*1).' * (PI()/180)) *
                SIN(('.($lon*1).' * (PI()/180)-longitude*(PI()/180))/2) *
                SIN(('.($lon*1).' * (PI()/180)-longitude*(PI()/180))/2)
                ),
            SQRT(1-(
                SIN(('.($lat*1).' * (PI()/180)-latitude*(PI()/180))/2) *
                SIN(('.($lat*1).' * (PI()/180)-latitude*(PI()/180))/2) +
                COS(latitude * (PI()/180)) *
                COS('.($lat*1).' * (PI()/180)) *
                SIN(('.($lon*1).' * (PI()/180)-longitude*(PI()/180))/2) *
                SIN(('.($lon*1).' * (PI()/180)-longitude*(PI()/180))/2)
            ))
        )
        )) <= '.($distance/1000). ')');
    }

    return '';


}
function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
    $earth_radius = 6371;

    $dLat = deg2rad($latitude2 - $latitude1);
    $dLon = deg2rad($longitude2 - $longitude1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;

    return $d;
}  
?>
