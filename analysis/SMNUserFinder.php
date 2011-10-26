<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SMNUserFinder {
    public function filterMostPopularFriends($max_distance=null, $lat=null, $long=null) {
        if($max_distance==null || $lat==null || $long == null) $users = SMNUserQueries::allUsers();
        else $users = SMNUserQueries::allUsersWithLocation();
        if($max_distance != null && $lat!=null && $long == null) $users = filterUsersByLocation($lat,$long,$max_distance,$users);
        $friends = array();
        foreach($users as $user) {
            $user_profile = new SMNProfile($user);
            $friends = array_merge($user_profile->showFriendUris(),$friends);
        }
        return SMNBalance::rank($friends);
    }
    
    public function mostPopularUsersFast() {
        $mostpopular = SMNUserQueries::mostPopularFriends();
        $cleaned = array();
        foreach ($mostpopular as $triple) {
            $cleaned[$triple['z']] = $triple['followers'];
        }
        return $cleaned;
    }
    public function commonUsersByTagsFast($user_uri) {
        $common = SMNUserQueries::usersByTagsFast($user_uri);
        $cleaned = array();
        foreach ($common as $triple) {
            $cleaned[$triple['z']] = $triple['tags'];
        }
        return $cleaned;
    }
    public function commonFriendsFast($user_uri) {
        $common = SMNUserQueries::usersByCommonFriendsFast($user_uri);
        $cleaned = array();
        foreach ($common as $triple) {
            $cleaned[$triple['z']] = $triple['friends'];
        }
        return $cleaned;
    }
    public function commonMentionsFast($user_uri) {
        $common = SMNUserQueries::usersByCommonMentionsFast($user_uri);
        $cleaned = array();
        foreach ($common as $triple) {
            $cleaned[$triple['z']] = $triple['mentions'];
        }
        return $cleaned;
    }
    public function usersByTags($user_uri,$event=false,$type=null) {
        if($type==null) $results = SMNUserQueries::usersByTagsFast($user_uri);
            else $results = SMNUserQueries::usersByEntitiesFast($user_uri,$type);
        $cleaned = array();
        foreach ($results as $triple) {
            $cleaned[$triple['z']] = $triple['tags'];
        }
        return $cleaned;
    }
    public function mostPopularMentionsFast() {
        $mostpopular = SMNUserQueries::mostPopularMentions();
        $cleaned = array();
        foreach ($mostpopular as $triple) {
            $cleaned[$triple['z']] = $triple['addressers'];
        }
        return $cleaned;
    }
    public function findRelatedUsers($user_uri, $max_distance=null, $lat=null, $long=null) {
        //TODO: add filter on the date of tweets considered
        $user_profile = new SMNProfile($user_uri);
        $user_uri = $user_profile->showUserUri();

        $tags = $user_profile->getTags();
        $events = $user_profile->getEvents();
        $origins = SMNUserQueries::tweetLocations($user_uri);
        $rankings = array();
        if(count($events)>0) {
            //$users['tags'] = SMNUserQueries::usersByTags($user_uri, $tags);
            $users['scientific_events'] = SMNUserQueries::usersByTags($user_uri, $events);
            foreach ($users as $key => $userpart) {
                if($userpart!=null) $rankings[$key] = SMNBalance::rank($userpart);
            }
        } else {
            //$rankings['tags'] = self::usersByTags($user_uri);
        }

        $rankings['tags'] = self::commonUsersByTagsFast($user_uri);
        $rankings['friends'] = self::commonFriendsFast($user_uri);
        $rankings['mentions'] = self::commonMentionsFast($user_uri);

        if ($max_distance != null) {
            if ($lat == null || $long == null)
                foreach ($rankings as $rankingspart) {
                    $userspart = array_keys($rankingspart);
                    $userspart = filterUsersByLocationOfUser($user_uri, $max_distance, $userspart);
                    $filtered = array();
                    foreach ($userspart as $user) {
                        $filtered[$user] = $rankingspart[$user];
                    }
                    $rankingspart = arsort($filtered,SORT_NUMERIC);
                }
            else
                foreach ($rankings as $rankingspart) {
                    $userspart = array_keys($rankingspart);
                    $userspart = filterUsersByLocation($lat, $long, $max_distance, $userspart);
                    $filtered = array();
                    foreach ($userspart as $user) {
                        $filtered[$user] = $rankingspart[$user];
                    }
                    $rankingspart = arsort($filtered,SORT_NUMERIC);
                }
        }

        return $rankings;
    }
    public function findRelatedUsersOld($user_uri, $max_distance=null, $lat=null, $long=null) {
        //TODO: add filter on the date of tweets considered
        $user_profile = new SMNProfile($user_uri);
        $user_uri = $user_profile->showUserUri();

        $tags = $user_profile->getInterests();
        $events = array_keys($user_profile->getEvents());
        $origins = SMNUserQueries::tweetLocations($user_uri);

//        $persons = $user_profile->showEntities("Person");
//        $general_events = $user_profile->showEntities("Event");
//        $organisations = $user_profile->showEntities("Organisation");
//        $places = $user_profile->showEntities("Place");

        $users['tags'] = SMNUserQueries::usersByTags($user_uri, $tags);
        $users['scientific_events'] = SMNUserQueries::usersByTags($user_uri, $events);
//        $users['general_events'] = SMNUserQueries::usersByTags($user_uri, $general_events);
//        $users['persons'] = SMNUserQueries::usersByTags($user_uri, $persons);
//        $users['organisations'] = SMNUserQueries::usersByTags($user_uri, $organisations);
//        $users['places'] = SMNUserQueries::usersByTags($user_uri, $places);
        $users['origins'] = SMNUserQueries::usersByTweetOrigin($user_uri, $origins);
        $users['friends'] = SMNUserQueries::usersByCommonFriends($user_uri, $friends);

        if ($max_distance != null) {
            if ($lat == null || $long == null)
                foreach ($users as $userspart)
                    $userspart = filterUsersByLocationOfUser($user_uri, $max_distance, $userspart);
            else
                foreach ($users as $userspart)
                    $userspart = filterUsersByLocation($lat, $long, $max_distance, $userspart);
        }


        $rankings = array();
        foreach ($users as $key => $userpart) {
            if($userpart!=null) $rankings[$key] = SMNBalance::rank($userpart);
        }
        //$ranking = SMNBalance::mergeRankings($rankings['tags'], $rankings['events'], $rankings['origins'], $rankings['friends']);
//        if (strcasecmp($operation, 'OR') == 0) {
        return $rankings;
//        } else {
//            $intersection = array_intersect($users['tags'], $users['events'], $users['origins'], $rankings['friends']);
//            $ranking_intersected = array();
//            foreach ($intersection as $intersection_user) {
//                $ranking_intersected[$intersection_user] = $ranking[$intersection_user];
//            }
//            return $ranking_intersected;
//        }
    }

    public function filterUsersByLocationOfUser($user_uri, $max_distance, $users = null) {
        if ($users == null)
            $users = SMNUserQueries::allUserUrisWithLocation();
        $location = SMNUserQueries::location($user_uri);
        $latitude = SMNLocation::latitude($location);
        $longitude = SMNLocation::longitude($location);
        if ($latitude != null && $longitude != null) {
            return self::filterUsersByLocation($latitude, $longitude, $max_distance, $users);
        } else
            return SMNUserQueries::allUserUris();
    }

    public function filterUsersByLocation($lat, $long, $max_distance, $unfiltered = null) {
        if ($unfiltered == null)
            $unfiltered = SMNUserQueries::allUserUrisWithLocation();
        $filtered = array();
        foreach ($unfiltered as $user_uri) {
            $location = SMNUserQueries::location($user_uri);
            $latitude = SMNLocation::latitude($location);
            $longitude = SMNLocation::longitude($location);
            if ($latitude != null && $longitude != null) {
                $distance = getDistance($lat, $long, $latitude, $longitude);
                if ($distance < $max_distance)
                    $filtered[] = $user_uri;
            }
        }
        return $filtered;
    }

    public function findUsersByEvent($event) {
        return null;//TODO: find users linking to an event (depending on Colinda);
    }
}

?>
