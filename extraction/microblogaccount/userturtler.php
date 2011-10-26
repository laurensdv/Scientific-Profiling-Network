<?php

class UserTurtler {

    protected $user;
    protected $userprofile;
    protected $usertimeline;
    protected $tweets = array();
    protected $geolocations = array();
    protected $tags = array();
    protected $friends = array();

    public function __construct($user, $doGeo = true, $doTag = true, $start_date=null) {
        $this->user = $user;
        $this->userprofile = annotateUserProfile($user);
        $this->tweets = annotateUserTweets($user,$start_date);
        $this->usertimeline = annotateUserTimeLine($this->userprofile, $this->tweets, $this->tags);
        if ($doGeo)
            $this->geolocations = annotateWithGeoData($this->userprofile, $this->tweets);
        if ($doTag)
            $this->tags = array_merge($this->tags, tagTweets($this->tweets, $this->userprofile, $this->usertimeline));
        //$this->findFriendsScreenNames();
        $userproperties = $this->userprofile->getProperties();
        //$this->friends = $userproperties[SIOC.'follows'];
    }

    private function findFriendsScreenNames() {
        $userproperties = $this->userprofile->getProperties();
        if (array_key_exists(SIOC . 'follows', $userproperties)) {
            $frienduris = $userproperties[SIOC . 'follows'];
            $friendsinfo = getMultiHttp($frienduris);
            foreach ($friendsinfo as $key => $friendinfo) {
                $friendall = json_decode($friendinfo, true);
                $screenname = $friendall['screen_name'];
                $this->friends[$key] = $screenname;
            }
        }
    }

    private function printHeader() {
        //DOCUMENT HEADER
        $output = "# This document is generated on Semantic Profiling Network.\n" .
                "# It is the N3 version of twitter user: " . $this->user . " and its tweets grabbed from Grabeeter.\n" .
                "\n\n" .
                "@prefix sioc: <" . SIOC . ">\n" .
                "@prefix sioc_t: <" . SIOCT . ">\n" .
                "@prefix foaf: <" . FOAF . ">\n" .
                "@prefix dct: <" . DCTERMS . ">\n" .
                "@prefix rdfs: <" . RDFS . ">\n" .
                "@prefix rdf: <" . RDF . ">\n" .
                "@prefix wgs84_pos: <" . POS . ">\n" .
                "@prefix gn: <" . GN . ">\n" .
                "@prefix ctag: <" . CTAG . ">\n" .
                "@prefix nt: <" . NT . ">\n" .
                PHP_EOL;
        return $output;
    }

    public function storeUserProfile() {
        $useraccount = '<' . $this->userprofile->getRoot() . '>';
        $usertriples = toTriples($this->userprofile);
        foreach ($usertriples as $triple) {
            $triple = rewriteTriple($triple);
            SMNStore::storeTripleNoPrefix($useraccount, $triple->getPredicate(), $triple->getObject());
        }
    }

    public function getUserFriends() {
        return $this->friends;
    }

    public function getUserProfile() {
        $output = $this->printHeader();
        $useraccount = $this->userprofile->getRoot();
        $usertriples = toTriples($this->userprofile);
        $output .= "<" . $useraccount . ">\n";
        $i = 0;
        foreach ($usertriples as $triple) {
            $line = "\t" . formatTriple($triple);
            $output .= $line;
            if (++$i == count($usertriples))
                $output .= " .\n";
            else
                $output .= ";\n";
        }
        $output .= PHP_EOL;
        return $output;
    }

    public function storeUserTimeLine() {
        $usertimeline = "<" . $this->usertimeline->getRoot() . ">";
        $timelinetriples = toTriples($this->usertimeline);
        foreach ($timelinetriples as $triple) {
            $triple = rewriteTriple($triple);
            SMNStore::storeTripleNoPrefix($usertimeline, $triple->getPredicate(), $triple->getObject());
        }
    }

    public function getUserTimeline() {
        $output = $this->printHeader();
        $usertimeline = $this->usertimeline->getRoot();
        $timelinetriples = toTriples($this->usertimeline);
        $output .= "<" . $usertimeline . ">\n";
        $i = 0;
        foreach ($timelinetriples as $triple) {
            $line = "\t" . formatTriple($triple);
            $output .= $line;
            if (++$i == count($timelinetriples))
                $output .= " .\n";
            else
                $output .= ";\n";
        }
        $output .= PHP_EOL;
        return $output;
    }

    public function storeAllTweets() {
        foreach ($this->tweets as $tweettree) {
            $tweetsubject = '<' . $tweettree->getRoot() . '>';
            $tweettriples = tweetTriples($tweettree);
            $storestring = "";
            foreach ($tweettriples as $triple) {
                $triple = rewriteTriple($triple);
                $storestring .= $tweetsubject.' '.$triple->getPredicate().' '.$triple->getObject().' . ';
                //SMNStore::storeTripleNoPrefix($tweetsubject, $triple->getPredicate(), $triple->getObject());
            }
            SMNStore::storeTriplesNoPrefix($storestring);
        }
    }

    public function getAllTweets() {
        $outputs = array();
        foreach ($this->tweets as $tweettree) {
            $output = $this->printHeader();
            $tweettriples = tweetTriples($tweettree);
            $output .= '<' . $tweettree->getRoot() . '>' . PHP_EOL;
            $i = 0;
            foreach ($tweettriples as $triple) {
                $line = "\t" . formatTriple($triple);
                $output .= $line;
                if (++$i == count($tweettriples))
                    $output .= " .\n";
                else
                    $output .= ";\n";
            }
            $output .= PHP_EOL;
            $outputs[$tweettree->getRoot()] = $output;
        }

        return $outputs;
    }

    public function getAllTweetsOld() {
        $output = $this->printHeader();
        foreach ($this->tweets as $tweettree) {
            $tweettriples = tweetTriples($tweettree);
            $output .= '<' . $tweettree->getRoot() . '>' . PHP_EOL;
            $i = 0;
            foreach ($tweettriples as $triple) {
                $line = "\t" . formatTriple($triple);
                $output .= $line;
                if (++$i == count($tweettriples))
                    $output .= " .\n";
                else
                    $output .= ";\n";
            }
            $output .= PHP_EOL;
        }
        $output .= PHP_EOL;
        return $output;
    }

    public function storeAllGeoLocations() {
        if ($this->geolocations != null) {
            foreach ($this->geolocations as $geotree) {
                $geosubject = '<' . $geotree->getRoot() . '>';
                $geotriples = geoTriples($geotree);
                $storestring = "";
                foreach ($geotriples as $triple) {
                    $triple = rewriteTriple($triple);
                    $storestring .= $geosubject.' '.$triple->getPredicate().' '.$triple->getObject().' . ';
                    //SMNStore::storeTripleNoPrefix($geosubject, $triple->getPredicate(), $triple->getObject());
                }
                SMNStore::storeTriplesNoPrefix($storestring);
            }
        }
    }

    public function getAllGeoLocations() {
        $output = '# No GeoLocations';
        if ($this->geolocations != null) {
            $output = $this->printHeader();
            foreach ($this->geolocations as $geotree) {
                $geotriples = geoTriples($geotree);
                $output .= '<' . $geotree->getRoot() . '>' . PHP_EOL;
                $i = 0;
                foreach ($geotriples as $triple) {
                    $line = "\t" . formatTriple($triple);
                    $output .= $line;
                    if (++$i == count($geotriples))
                        $output .= " .\n";
                    else
                        $output .= ";\n";
                }
                $output .= PHP_EOL;
            }
            $output .= PHP_EOL;
        }
        return $output;
    }

    public function storeAllTags() {
        if ($this->tags != null) {
            foreach ($this->tags as $tagtree) {
                $tagsubject = '<' . $tagtree->getRoot() . '>';
                $tagtriples = tagTriples($tagtree);
                $storestring = "";
                foreach ($tagtriples as $triple) {
                    $triple = rewriteTriple($triple);
                    $storestring.= $tagsubject.' '.$triple->getPredicate().' '.$triple->getObject().' . ';
                    //SMNStore::storeTripleNoPrefix($tagsubject, $triple->getPredicate(), $triple->getObject());
                }
                SMNStore::storeTriplesNoPrefix($storestring);

            }
        }
    }

    public function getAllTags() {
        if ($this->tags != null) {
            $output = $this->printHeader();
            foreach ($this->tags as $tagtree) {
                $tagtriples = tagTriples($tagtree);
                $output .= '<' . $tagtree->getRoot() . '>' . PHP_EOL;
                $i = 0;
                foreach ($tagtriples as $triple) {
                    $line = "\t" . formatTriple($triple);
                    $output .= $line;
                    if (++$i == count($tagtriples))
                        $output .= " .\n";
                    else
                        $output .= ";\n";
                }
                $output .= PHP_EOL;
            }
            $output .= PHP_EOL;
        } else
            $ouput="#No tags";
        return $output;
    }

    public function store() {
        $this->storeUserProfile();
        $this->storeUserTimeLine();
        $this->storeAllTweets();
        $this->storeAllTags();
        $this->storeAllGeoLocations();
    }

}

?>