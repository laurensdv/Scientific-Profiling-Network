<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNCommunity
 *
 * @author laurens
 */
class SMNCommunity {
    public function discoverCommunities() {
        $all_users = SMNUserQueries::allUserUris();
        $remaing_users = $all_users;
        $all_screennames = SMNUserQueries::allUsers();
        $couples = array();

        foreach($remaing_users as $useruri) {
            $couples[$user_uri]=findCouples($useruri);
        }

    }

    public function findCouples($user_uri) {
        $couples = array();
        $frienduris = array_intersect($all_users,SMNUserQueries::friends($useruri));
        foreach($frienduris as $frienduri) {
              $ffrienduris = array_intersect($all_users,SMNUserQueries::friends($frienduri));
              if(array_search($user_uri,$ffrienduris)) $couples[]=$frienduri;
        }
        return $couples;

    }
}
?>
