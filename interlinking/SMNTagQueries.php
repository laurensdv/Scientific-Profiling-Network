<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNTagQueries
 *
 * @author laurens
 */
class SMNTagQueries {

    //put your code here
    public function tagUri($tag_name) {
        $query = 'SELECT ?uri
                            WHERE  {
                            ?uri ctag:label "' . $tag_name . '"
                            }
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['uri'];
    }

    public function allHashTags() {
        $query = 'SELECT ?z
                            WHERE  {
                            ?t rdf:type ctag:AuthorTag .
                            ?t ctag:label ?z
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

//    public function unlinkedTags() {
//        $query = 'SELECT ?z
//                            WHERE  {
//                            ?t ctag:label ?z .
//                            OPTIONAL { ?t rdf:type ?uri .
//                                    FILTER regex(?uri,"' . DBP . '","i") } .
//                            FILTER ( !bound(?uri) ) }
//                            }';
//        $res = SMNStore::query($query);
//        return SMNStore::cleanUpSingleResult($res);
//
//    }
    public function isEntity($tag_name, $type="") {
        $query = 'SELECT ?z
                            WHERE  {
                            ?t rdf:type ?type .
                            ?t ctag:label ?z .
                            FILTER regex(?z,"' . $tag_name . '","i") .
                            FILTER regex(?type,"' . DBP . $type . '","i")
                            }';
        $res = SMNStore::query($query);
        $res = SMNStore::cleanUpSingleResult($res);
        return count($res) > 0;
    }

    public function conferenceUri($conference_code) {
        $query = 'SELECT ?uri
                            WHERE  {
                            ?t ctag:label "' . $conference_code . '" .
                            ?t ctag:means ?uri
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return false;
        else
            return $res[0]['uri'];
    }

    public function mostPopularConferences() {
        $query = 'SELECT DISTINCT ?z COUNT(?uri) AS ?users WHERE {
            ?t sioc:has_creator ?uri .
?t sioc:tagged ?y .
?y ctag:label ?z .
?y rdf:type swrc:Conference .
}
GROUP BY ?z
ORDER BY DESC(?users)
                    LIMIT ' . 25;
        $res = SMNStore::query($query);
        return $res;
    }

    public function tagUsers($tag_name) {
        $query1 = 'SELECT ?z
                            WHERE  {
                            ?tweet sioc:has_creator ?z .
                            ?tweet sioc:tagged ?y .
                            ?y ctag:label "' . $tag_name . '" .
                            ?y rdf:type swrc:Conference .
                            } GROUP BY ?z';
        $res1 = SMNStore::query($query1);
//        $query2 = 'SELECT ?z
//                            WHERE  {
//                            ?z foaf:topic ?autotag
//                            ?autotag rdf:type ctag:AutoTag .
//                            ?autotag ctag:label ?label .
//                            FILTER regex(?label,"' . $tag_name . '","i")
//                            }';
//        $res2 = SMNStore::query($query2);
//        $query3 = 'SELECT ?z
//                            WHERE  {
//                            ?z sioc:topic ?autotag
//                            ?autotag rdf:type ctag:AutoTag .
//                            ?autotag ctag:label ?label .
//                            FILTER regex(?label,"' . $tag_name . '","i")
//                            }';
//        $res3 = SMNStore::query($query3);
//        $res = array_merge($res1, $res2);
//        $res = array_merge($res, $res3);
        return SMNStore::cleanUpSingleResult($res1);
    }

    function tagDetails($tag_uri) {
        return SMNStore::describe($tag_uri);
    }

}

?>
