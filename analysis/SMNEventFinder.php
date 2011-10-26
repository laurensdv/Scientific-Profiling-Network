<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SMNEventFinder {
    public function mostPopularEvents() {
        $mostpopular = SMNTagQueries::mostPopularConferences();
        $cleaned = array();
        foreach ($mostpopular as $triple) {
            $cleaned[$triple['z']] = $triple['users'];
        }
        return $cleaned;
    }
    public function findRelatedEvents($user_uri, $date_begin=null,$date_end=null,$max_distance=null, $lat=null, $long=null) {
        $user_profile = new SMNProfile($user_uri); //TODO: add filter for tweets considered by date

        $user_events = $user_profile->getEvents();

        $friend_events = SMNUserQueries::friendEvents($user_profile->showScreenName());

        $events = array_diff($friend_events, $user_events);

        if($date_begin!=null && $date_end!=null) {
            $events = self::filterByDate($date_begin,$date_end,$events);
        }

        if ($max_distance != null) {
            if ($lat == null || $long == null) {
                $location = SMNUserQueries::location($user_uri);
                $latitude = SMNLocation::latitude($location);
                $longitude = SMNLocation::longitude($location);
                if($latitude!=null && $longitude!=null) {
                     $events = self::filterByLocation($latitude,$longitude,$max_distance,$events);
                }
            }
            else
                $events = self::filterByLocation($lat,$long,$max_distance,$events);
        }

        return SMNBalance::rank($events);

    }
    public function filterByDate($date_begin,$date_end,$events) {
        return $events;
    }
    public function filterByLocation($lat,$long,$max_distance,$events) {
        return $events;
    }
    public function filterByUsers($users,$events = null) {
        $filtered = array();
        if($events == null) $events = array();
        foreach ($users as $user) {
            $profile = new SMNProfile($user);
            $p_events = $profile->showEvents();
            $p_events = array_intersect($p_events, $events);
            $filtered = array_merge($filtered,$p_events);
        }
        return $filtered;
    }
}
?>
