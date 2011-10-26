<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNLsi
 *
 * @author laurens
 */
require_once 'includes.php';

class SMNLsi {
    protected $idfweights = array();
    protected $tfidfs = array();
    
    public function __construct() {
        $this->updateWeights();
    }
    public function updateWeights() {
        $this->idfWeights();
        //$this->calculateAllTfIdfWeights();
    }
    private function tfidfWeights($user) {
        $tfidf_weights = array();
        $tfidfs = array();
        $usertags = SMNTagFilter::filter(SMNUserQueries::tags($user));

        //TFS
        $term_counts = array_count_values($usertags);
        $term_cardinality = count($usertags);

        //IDFS
        foreach($term_counts as $tag => $count) {
            $tfidfs[$tag] = (1.0*$count)/$term_cardinality * ($this->idfweights[$tag]);
        }
        $this->tfidfs[$user] =  $tfidfs;
    }
    private function calculateAllTfIdfWeights() {
        $users = SMNUserQueries::allUsers();
        foreach ($users as $user)  {
            $this->tfidfWeights($user);
        }
    }
    public function getTFIDF($user) {
        $this->tfidfWeights($user);
        return $this->tfidfs[$user];
    }
    private function idfWeights() {
        
        $tags = SMNTagFilter::filter(SMNTagQueries::allHashTags());
        $users = SMNUserQueries::allUsers();
        $D = count($users);
        foreach ($users as $user) {
            $usertags[$user] = SMNTagFilter::filter(SMNUserQueries::tags($user));
        }
        //IDFS
        foreach ($tags as $tag) {
            $df = 0;
            foreach($users as $user) {
                if(in_array($tag, $usertags[$user])) $df++;
            }
            $this->idfweights[$tag]= log($D/(1.0+$df));

        }
    }
}
?>
