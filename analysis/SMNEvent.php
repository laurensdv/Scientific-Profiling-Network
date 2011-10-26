<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNEvent
 *
 * @author laurens
 */
class SMNEvent {
      private $uri;
      private $name;
      private $code;
      private $venue_name;
      private $description;
      private $location;
      private $latitude;
      private $longitude;
      private $keywords;
      private $date_begin;
      private $date_end;

      public function __construct($code) {
        $this->code=$code;
        $this->uri = SMNTagQueries::conferenceUri($code);
      }

      public function showUri() {
          return $this->uri;
      }

      public function showUsers() {
          return SMNTagQueries::tagUsers($this->code);
      }
}
?>
