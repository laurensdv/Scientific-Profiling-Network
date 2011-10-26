<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNPerson
 *
 * @author laurens
 */
class SMNPerson {
      private $accounts;
      public function __construct($user) {
          $this->accounts = SMNPersonQueries::accounts($user);
      }
      public function showAccounts() {
          return $this->accounts;
      }
}
?>
