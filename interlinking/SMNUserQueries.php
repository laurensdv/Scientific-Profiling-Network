<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNUserQueries
 *
 * @author laurens
 */
class SMNUserQueries {
    public function userScreenName($something) {
        $query = 'SELECT ?screen
                            WHERE  {
                            ?uri foaf:nickname ?screen .
                            ?uri ?predicate "' . $something . '"
                            }
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['screen'];
    }
    public function userScreenNameByUri($something) {
        $query = 'SELECT ?screen
                            WHERE  {
                            "'.$something.'" foaf:nickname ?screen .
                            }
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['screen'];
    }

    public function userName($something) {
        $query = 'SELECT ?name
                            WHERE  {
                            ?uri foaf:name ?name .
                            ?uri ?predicate "' . $something . '"
                            }
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['name'];
    }
        public function userNameByScreen($something) {
        $query = 'SELECT ?name
                            WHERE  {
                            ?uri foaf:name ?name .
                            ?uri foaf:nickname "' . $something . '"
                            }
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['name'];
    }
    public function latestTweetDate($screen_name) {
        $query = 'SELECT ?date
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?tweet sioc:has_creator ?uri .
                            ?tweet dct:created ?date
                            }
                            ORDER BY DESC(?date)
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['date'];
    }
    public function latestTweet($screen_name) {
        $query = 'SELECT ?tweet
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?tweet sioc:has_creator ?uri .
                            ?tweet dct:created ?date
                            }
                            ORDER BY DESC(?date)
                            LIMIT 1';
        $res = SMNStore::query($query);
        return $res[0]['tweet'];
    }
    public function userInfo($screen_name) {
//           		$query = 'SELECT ?uri ?p ?y
//                            WHERE  {
//                            ?uri foaf:nickname "'.$screen_name.'" .
//                            ?uri ?p ?y
//                            }';
        $uri = SMNUserQueries::userUri($screen_name);
        $rs = SMNStore::describe($uri);
        $triples = ARC2::getTriplesFromIndex($rs);
        $index = ARC2::getSimpleIndex($triples, true);
        //$res = $rs[$uri];
        return $index;
    }

    public function tags($screen_name) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?t sioc:has_creator ?uri .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?z
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    public function mentions($screen_name) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?t sioc:has_creator ?uri .
                            ?t sioc:addressed_to ?z
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    public function entities($screen_name,$entity_name) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?t sioc:has_creator ?uri .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?z .
                            ?y rdf:type ?type .
                            FILTER regex(?type,"' . $entity_name . '","i")
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    public function friendEvents($screen_name) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?uri sioc:follows ?furi .
                            ?t sioc:has_creator ?furi .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?z .
                            ?y rdf:type ?type .
                            FILTER regex(?type,"Conference","i")
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    
    public function usersByEntitiesFast($user_uri,$type) {
        $query = 'SELECT DISTINCT ?z COUNT(?l) as ?tags WHERE  {
                            ?t sioc:has_creator <'.$user_uri.'> .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?l .
                            ?y rdf:type ?type .
                            FILTER regex(?type,"' . $type . '","i") .
                            ?tw sioc:tagged ?y .
                            ?tw sioc:has_creator ?z
                            }  GROUP BY ?z
                            ORDER BY DESC(?tags)';
        $res = SMNStore::query($query);
        return $res;
    }
    public function tagsFiltered($screen_name,$date_begin,$date_end) {
        $date_begin = 'xsd:dateTime(' . $date_begin->format('Y-m-d\TH:i:sP') . ')';
        $date_end = 'xsd:dateTime(' . $date_end->format('Y-m-d\TH:i:sP') . ')';
        //or try xsd:dateTime($date_whatever)
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?t sioc:has_creator ?uri .
                            ?t dct:created ?date .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?z .
                            FILTER (?date > '.$date_begin.' && ?date < '.$date_end.')
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    public function allUsers() {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname ?z .
                            ?uri rdf:type sioc:UserAccount
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    public function mostPopularFriends() {
        $query = 'SELECT ?z COUNT(?uri) AS ?followers WHERE {
                            ?uri rdf:type sioc:UserAccount .
                            ?uri sioc:follows ?z
                    }
                    GROUP BY ?z
                    ORDER BY DESC(?followers)
                    LIMIT '.FRIENDS_LIMIT;
        $res = SMNStore::query($query);
        return $res;

    }
        public function mostPopularMentions() {
        $query = 'SELECT DISTINCT ?z COUNT(?uri) AS ?addressers WHERE {
                            ?uri rdf:type sioc:UserAccount .
                            ?t sioc:has_creator ?uri.
                            ?t sioc:addressed_to ?z .
                    }
                    GROUP BY ?z
                    ORDER BY DESC(?addressers)
                    LIMIT '.FRIENDS_LIMIT;
        $res = SMNStore::query($query);
        return $res;

    }
        public function allUsersWithLocation() {
        $query = 'PREFIX aGeo: <http://linkeddata.semanticprofiling.net/scripts/geo#>
                    SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname ?z .
                            ?uri rdf:type sioc:UserAccount .
                            ?uri dct:spatial ?location .
                            FILTER isUri(?location)
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    public function allUserUris() {
        $query = 'SELECT ?z
                            WHERE  {
                            ?z foaf:nickname ?nick .
                            ?z rdf:type sioc:UserAccount
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    public function allUsersFiltered($lat,$long,$distance) {
        $query = 'PREFIX aGeo: <http://linkeddata.semanticprofiling.net/scripts/geo#>
                    SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname ?z .
                            ?uri rdf:type sioc:UserAccount .
                            FILTER ( aGeo:distance('.$long.', '.$lat.', ?bxLoc, ?byLoc) < '.$distance.' )
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    public function allUserUrisWithLocation() {
        $query = 'PREFIX aGeo: <http://linkeddata.semanticprofiling.net/scripts/geo#>
                    SELECT ?z
                            WHERE  {
                            ?z foaf:nickname ?nick .
                            ?z dct:spatial ?location .
                            FILTER isUri(?location)
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    public function usersByTags($user_uri,$tags) {
        $users = array();
        foreach($tags as $tag) {
            $users = array_merge($users,SMNTagQueries::tagUsers($tag));
        }
        return $users;
    }
    public function usersByTagsFast($user_uri) {
        $query = 'SELECT DISTINCT ?z COUNT(?l) as ?tags WHERE  {
                            ?t sioc:has_creator <'.$user_uri.'> .
                            ?t sioc:tagged ?y .
                            ?y ctag:label ?l .
                            ?tw sioc:tagged ?y .
                            ?tw sioc:has_creator ?z
                            }  GROUP BY ?z
                            ORDER BY DESC(?tags)
                            LIMIT '.(25);
        $res = SMNStore::query($query);
        return $res;
    }
    public function usersByTweetOrigin($user_uri,$origins=null) {
        if($origins==null) $origins = self::tweetLocations(self::userScreenName($user_uri));
        $users = array();
        foreach($origins as $origin) {
            $users = array_merge($users,SMNLocation::locationUsers($origin));
        }
        return $users;
    }
    public function usersByCommonFriends($user_uri,$friends=null) {
        if($friends==null) $friends = self::friends($user_uri);
        $users = array();
        foreach ($friends as $friend) {
            $users = array_merge($users,self::followers($friend));
        }
        return $users;
    }
    public function usersByCommonFriendsFast($user_uri) {
        $query = 'SELECT ?z COUNT(?uri) AS ?friends WHERE {
                            <'.$user_uri.'> sioc:follows ?uri .
                            ?z rdf:type sioc:UserAccount .
                            ?z sioc:follows ?uri
                    }
                    GROUP BY ?z
                    ORDER BY DESC(?friends)
                    LIMIT '.(25);
        $res = SMNStore::query($query);
        return $res;
    }
    public function usersByCommonMentionsFast($user_uri) {
        $query = 'SELECT DISTINCT ?z COUNT(?uri) AS ?mentions WHERE {
                            ?t sioc:has_creator <'.$user_uri.'> .
                            ?t sioc:addressed_to ?uri .
                            ?z rdf:type sioc:UserAccount .
                            ?tw sioc:has_creator ?z .
                            ?tw sioc:addressed_to ?uri
                    }
                    GROUP BY ?z
                    ORDER BY DESC(?mentions)
                    LIMIT '.(25);
        $res = SMNStore::query($query);
        return $res;
    }
    
    public function userUri($screen_name) {
        $query = 'SELECT ?uri
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?uri rdf:type sioc:UserAccount
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return false;
        else
            return $res[0]['uri'];
    }

    public function numberOfFriends($screen_name) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?uri foaf:nickname "' . $screen_name . '" .
                            ?uri foaf:follows ?z
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return false;
        else
            return $res[0]['z'];
    }

    public function personUri($screen_name) {
        $query = 'SELECT ?uri
                            WHERE  {
                            ?account foaf:nickname "' . $screen_name . '" .
                            ?uri foaf:account ?account
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return false;
        else
            return $res[0]['uri'];
    }

    function location($user_uri) {
                $query = 'SELECT ?uri
                            WHERE  {
                                <' . $user_uri . '> dct:spatial ?uri
                            }
                            LIMIT 1';
        if (!$res = SMNStore::query($query))
            return false;
        else
            return $res[0]['uri'];
    }
    function tweetLocations($user_uri) {
        //TODO: add possibility for date filter
        $query = 'SELECT ?z
                            WHERE  {
                            ?z sioc:has_creator <'.$user_uri.'> .
                            ?tweet dct:spatial ?location .
                            ?location rdfs:label ?z
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
    function profile($user_uri) {
        return SMNStore::describe($user_uri);
    }

    // List of followers
    function followers($user_uri) {
        $pattern = '?z sioc:follows <' . $user_uri . '>';
        return SMNUserQueries::people('followers', $pattern);
    }

    function friends($user_uri) {
        $pattern = '<' . $user_uri . '> sioc:follows ?z';
        return SMNUserQueries::people('friends', $pattern);
    }

    function description($user_uri) {
        $pattern = '<' . $user_uri . '> dct:description ?z';
        return SMNUserQueries::people('description', $pattern);
    }

    function image($user_uri) {
        $pattern = '<' . $user_uri . '> foaf:img ?z';
        return SMNUserQueries::people('image', $pattern);
    }

    function people($type, $pattern) {
        $query = "SELECT ?z WHERE { $pattern } LIMIT ".(FRIENDS_LIMIT);
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }

    function &either() {
        $arg_list = func_get_args();
        foreach ($arg_list as $i => $arg) {
            if ($arg) {
                return $arg_list[$i];
            }
        }
        return null;
    }

}

?>
