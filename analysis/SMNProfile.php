<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNProfile
 *
 * @author laurens
 */
class SMNProfile {
      private $uri;
      private $name;
      private $screenname;
      private $image;
      private $friends;
      private $description;
      private $location;

      public function __construct($user){
        $this->uri = SMNUserQueries::userUri($user);
        $this->screenname = $user;
        $this->name = SMNUserQueries::userName($user);
        $this->friends = SMNUserQueries::friends($this->uri);
        $this->description = SMNUserQueries::description($this->uri);
        $this->location = SMNUserQueries::location($this->uri);
        $this->image = SMNUserQueries::image($this->uri);
      }
      public function showUserUri() {
          return $this->uri;
      }
      public function showScreenName() {
          return $this->screenname;
      }
      public function showName() {
          return $this->name;
      }
      public function showDescription(){
         return $this->description;
      }
      
      public function showImage() {
         return $this->image;
      }
      public function showLatestTweet() {
         return SMNUserQueries::latestTweet($this->screenname);
      }
      public function showFriendUris() {
         return $this->friends;
      }
      public function showFriends() {
          $friendslist = array();
         
          $friendsinfo = getMultiHttp($this->friends);
          if(!is_array($friendsinfo)||$friendsinfo==null) return $this->friends;
	  foreach ($friendsinfo as $key => $friendinfo) {
		$friendall = json_decode($friendinfo,true);
                if(!is_array($friendall)||!array_key_exists('screen_name', $friendall)) return $this->friends;
        	$screenname = $friendall['screen_name'];
		$friendslist[$key] = $screenname;
	  }
          return $friendslist;
      }
      public function getEvents() {
          $events = SMNUserQueries::entities($this->screenname,'Conference');
          return array_unique($events);
      }
      public function getEntities($type) {
        $entities = SMNTagFilter::filter(SMNUserQueries::entities($this->screenname,$type));
        return array_unique($entities);
      }
      public function showMentions() {
        $mentions = SMNUserQueries::mentions($this->screenname);
        return SMNBalance::rank($mentions);
      }
      public function showEntities($type) {
          $entities = SMNTagFilter::filter(SMNUserQueries::entities($this->screenname,$type));
          return SMNBalance::rank($entities);
      }
      public function getInterests() {
          $interests = SMNTagFilter::interestFilter(SMNUserQueries::tags($this->screenname));
          return array_unique($interests);
      }
      public function getTags() {
          $tags = SMNTagFilter::interestFilter(SMNUserQueries::tags($this->screenname));
          return array_unique($tags);
      }
      public function showEvents($date_begin=null,$date_end=null) {
          if($date_begin==null&&$date_end==null)
            $events = SMNUserQueries::entities($this->screenname,'Conference');
            else return self::showEventsByDate($date_begin,$date_end);
          return SMNBalance::rank($events);
      }
      public function showEventsByDate($date_begin,$date_end) {
          $events = SMNUserQueries::entities($this->screenname,'Conference'); //TODO: Date filter
          return SMNBalance::rank($events);
      }
      public function showInterests($date_begin=null,$date_end=null) {
          if($date_begin==null&&$date_end==null) {
            $events=self::getEvents();
            $alltags= SMNUserQueries::tags($this->screenname);
            $tags = SMNTagFilter::filter(array_diff($alltags,$events));
          }
          else return self::showTagsByDate($date_begin,$date_end);
          return SMNBalance::rank($tags);
      }
      public function showTags($date_begin=null,$date_end=null) {
          if($date_begin==null&&$date_end==null)
            $tags = SMNTagFilter::filter(SMNUserQueries::tags($this->screenname));
          else return self::showTagByDate($date_begin,$date_end);
          return SMNBalance::rank($tags);
      }
      public function showTagsByDate($date_begin=null,$date_end=null) {
          $tags = SMNTagFilter::filter(SMNUserQueries::tags($this->screenname,$date_begin,$date_end));
          return SMNBalance::rank($tags);
      }
      public function showInterestsByDate($date_begin,$date_end) {
          $interests = SMNTagFilter::interestFilter(SMNUserQueries::tags($this->screenname,$date_begin,$date_end));
          return SMNBalance::rank($interests);
      }
      public function showLocation() {
          if(!strstr($this->location, 'http://ws.geonames.org/searchJSON?q='))
                  return $this->location;
          else return SMNLocation::describe($this->location);
      }
}
?>
