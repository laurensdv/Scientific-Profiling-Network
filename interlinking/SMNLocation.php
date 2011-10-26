<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNLocation
 *
 * @author laurens
 */
class SMNLocation {
    function describe($uri) {
        return SMNStore::query("DESCRIBE <$uri>", true);
    }
    function latitude($uri) {
        $query = 'SELECT ?lat
                            WHERE  {
                            <' . $uri . '> wgs84_pos:lat ?lat
                            }
                            LIMIT 1';
         if (!$res = SMNStore::query($query))
            return null;
        else
            return $res[0]['lat'];
    }
    function longitude($uri) {
        $query = 'SELECT ?long
                            WHERE  {
                            <' . $uri . '> wgs84_pos:long ?long
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return null;
        else
        return $res[0]['long'];
    }

    public function locationUsers($location) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?z sioc:has_creator ?tweet .
                            ?tweet dct:spatial ?location .
                            ?location rdfs:label ?label .
                            FILTER regex(?label,"' . $location . '","i")
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
}
?>
