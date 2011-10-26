<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNOpenDataLinker
 *
 * @author laurens
 */
class SMNOpenDataLinker {

    private function geoNames($objects) {
        $geoinfos = array();
        $geourls = array();
        $geoalls = array();
        foreach ($objects as $loc_string) {
            if (strlen($loc_string) == 3)
                $loc_string = SMNOpenDataLinker::convertCountryCode($loc_string);
            $geo_url = GEO_URI . $loc_string;
            $geo_url = preg_replace('/\s*/m', '', $geo_url);
            $geourls[$loc_string] = $geo_url . "&maxRows=1&style=short";
        }
        $georaws = getMultiHttp($geourls);
        foreach ($georaws as $key => $georaw) {
            $geoall = json_decode($georaw, true);
            if ($geoall['totalResultsCount'] > 0) {
                $loc_string = array_search($key, $geourls);
                $geoinfos[$loc_string] = $geoall['geonames'][0];
            }
        }
    }

    private function convertCountryCode($iso3) {
        $countries = readCountriesCSV();
        $country = $countries[$iso3];
        if ($country)
            $iso3 = $country[4];
        return $iso3;
    }

    //use dbpedia rdf type
    public function dbPediaMeanings($objects) {
        $tagmeanings = array();
        foreach ($objects as $object) {
            $hashtaguris[$object] = 'http://lookup.dbpedia.org/api/search.asmx/KeywordSearch?QueryString='.$object;
        }
        $meanings = getMultiHttp($hashtaguris);

        foreach ($hashtaguris as $tag => $url) {
            $meaningall = xml2array($meanings[$tag]);
            if (array_key_exists('Result', $meaningall['ArrayOfResult'])) {
                $tagmeanings[$tag]['uri'] = $meaningall['ArrayOfResult']['Result']['URI'];
                if(array_key_exists('Class',$meaningall['ArrayOfResult']['Result']['Classes'])) {
                    foreach($meaningall['ArrayOfResult']['Result']['Classes']['Class'] as $class) {
                        $tagmeanings[$tag]['type'][]=$class['URI'];
                    }
                }
            }
        }
        return $tagmeanings;
    }

    public function colindaMeanings($objects) {
        $conftags = array();
        $results = SMNConference::findConferencesColinda($objects);
        foreach($results as $key => $result) {
            $decoded_result = json_decode($result);
            if($decoded_result != null) {
                foreach($decoded_result as $uri => $value) {
                    $value = object2array($value);
                    $object = $value['http://www.w3.org/2000/01/rdf-schema#label'][0]->value;
                    SMNStore::storeTriple("<" . TAGS_URI . urlencode($object) . ">", "ctag:means", "<".$uri.">");
                    SMNStore::storeTriple("<" . TAGS_URI . urlencode($object) . ">", "rdf:type", "swrc:Conference");
                    SMNStore::updateTriple("<" . TAGS_URI . urlencode($object) . ">", "ctag:label", '"'.$object.'"');
                    SMNStore::storeTriple("<" . TAGS_URI . urlencode($key) . ">", "ctag:means", "<".$uri.">");
                    SMNStore::storeTriple("<" . TAGS_URI . urlencode($key) . ">", "rdf:type", "swrc:Conference");
                    SMNStore::updateTriple("<" . TAGS_URI . urlencode($key) . ">", "ctag:label", '"'.$object.'"');
                    $conftags[$object] = $uri;
                    $conftags[$key] = $uri;
                    }
            }
        }
        return $conftags;
        }     

    private function friendsFromTwitter($user) {
        // create instance
        $twitter = new Twitter('32FoG0kITEE2Pq44Fi7Hzg', 'Ps7m3621i4ywdZce6dKTupfgJrI2LTPwTJmTFgMM');

        // set tokens
        $twitter->setOAuthToken('77273706-Wa8wXj7JAly0nQ2RMvn74CeiLM4B88pkHDaYIrvMz');
        $twitter->setOAuthTokenSecret('wc8gfhWzUj3V6JGRZ8UmJak1XKyzWlWZHSc3D2of9E');

        //$user = $_GET['user'];
        $userdata = $twitter->usersShow($user);
        $userfriends = $twitter->friendsIds($user);
        $userfollows = array();

        foreach ($userfriends as $userfriend) {
            $userfollows[$userfriend] = TWITTER_URI . $userfriend;
        }
        return $userfollows;
    }

    public function interlinkTags($objects) {
        $basetaguri = TAGS_URI;
        $dbpediameanings = SMNOpenDataLinker::dbPediaMeanings($objects);
        $geonames = SMNOpenDataLinker::geoNames($objects);

        //DBPedia
        foreach ($dbpediameanings as $tag => $meaning) {
            $tag = str_replace(' ', '_', $tag);
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "ctag:means", "<" . $meaning['uri'] . ">");
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "ctag:label", '"' . $tag . '"');
            if(array_key_exists('type', $meaning)) {
            foreach($meaning['type'] as $type) {
                if($type == DBP.'Person' || $type == DBP.'Event' || $type == DBP.'Organisation' || $type== DBP.'Place')
                    SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "rdf:type", "<". $type. ">");
                    SMNStore::storeTriple("<" . $basetaguri . $tag . ">", "rdf:type", "ctag:AuthorTag");
            }
            }
        }
        //GeoNames
        if($geonames != null) {
        foreach ($geonames as $tag => $geoname) {
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "rdfs:SeeAlso", "<" . "http://sws.geonames.org/" . $geoname['geonameId'] . "/about.rdf" . ">");
            SMNStore::storeTriple("<" . $basetaguri . $tag . ">", "rdf:type", "wgs84_pos:Point");
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "gn:countryCode", '"' . $geoname['countryCode'] . '"');
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "wgs84_pos:long", '"' . $geoname['lng'] . '"');
            SMNStore::updateTriple("<" . $basetaguri . $tag . ">", "wgs84_pos:lat", '"' . $geoname['lat'] . '"');
        }
        }

        
    }

    public function interlinkUser($user) {
        $nfriends = (int)SMNUserQueries::numberOfFriends($user);
        $lfriends = count(SMNUserQueries::friends(SMNUserQueries::userUri($user)));
        echo $user." has ".$lfriends." already linked friends and ".$nfriends." friends in total".PHP_EOL;
        if ($nfriends < FRIENDS_LIMIT && $lfriends < FRIENDS_LIMIT/2) {
            $friends = self::friendsFromTwitter($user);
            $user_uri = SMNUserQueries::userUri($user);
            SMNStore::removeProperty('<' . $user_uri . '>', 'sioc:follows');
            if(count($friends < FRIENDS_LIMIT)) {
                foreach ($friends as $friend) {
                    SMNStore::storeTriple('<' . $user_uri . '>', 'sioc:follows', '<' . $friend . '>');
                }
            }
        }
    }

}

?>
