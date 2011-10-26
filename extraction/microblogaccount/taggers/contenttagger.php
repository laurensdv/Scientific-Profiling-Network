<?php

global $tagMeanings;
$tagMeanings = array();

function calaisQuery($querycontent) {
    define("CALAIS_KEY", "ahhqt4ehcyxtup44vcq9h4zy");
    // Input/output formats
    $contentType = "text/raw"; // simple text - try also text/html
    $outputFormat = "Application/JSON"; // simple output format - try also xml/rdf and text/microformats
    $rdfAccessible = "true";
    $paramsXML = "<c:params xmlns:c=\"http://s.opencalais.com/1/pred/\" " .
            "xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"> " .
            "<c:processingDirectives c:contentType=\"" . $contentType . "\" " .
            "c:enableMetadataType=\"" . "GenericRelations,SocialTags" . "\" " .
            "c:outputFormat=\"" . $outputFormat . "\" " .
            "c:docRDFaccessible=\"" . $rdfAccessible . "\" " .
            "></c:processingDirectives> " .
            "<c:userDirectives c:allowDistribution=\"true\" " .
            "c:allowSearch=\"true\" c:externalID=\" \" " .
            "c:submitter=\"Semantic Profiling Network\"></c:userDirectives> " .
            "<c:externalMetadata><c:Caller>Calais REST Sample</c:Caller>" .
            "</c:externalMetadata></c:params>";
    // Construct the POST data string
    $data = "licenseID=" . urlencode(CALAIS_KEY);
    $data .= "&paramsXML=" . urlencode($paramsXML);
    $data .= "&content=" . urlencode($querycontent);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, CALAIS);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function tagUserProfile(&$usertree) {
    $userprops = $usertree->getProperties();
    $userdescription = $userprops[DCTERMS . 'description'];
    $tagtripletrees = array();
    //Tag user profile
    if ($userdescription != null) {
        $tagsraw = calaisQuery($userdescription);
        $tagall = json_decode($tagsraw, true);

        $docid = $tagall["doc"]["info"]["docId"];
        $language = $tagall["doc"]["meta"]["language"];
        $socialtags = array();
        $categories = array();
        $entities = array();
        $resources = array();

        if ($tagall != null) {
            foreach ($tagall as $tagkey => $taginfo) {
                if (array_key_exists("_typeGroup", $taginfo)) {
                    if ($taginfo["_typeGroup"] == "topics") {
                        $name = $taginfo["categoryName"];
                        if (!$name == "")
                            $categories[$name] = $taginfo["category"];
                    }
                    if ($taginfo["_typeGroup"] == "socialTag") {
                        $name = $taginfo["name"];
                        if (!$name == "")
                            $socialtags[$name] = $name;
                    }
                    //Later link entities in text to their connected identifiers such as URL or Company
                    if ($taginfo["_typeGroup"] == "entities") {
                        $name = $taginfo["name"];
                        $type = $taginfo["_type"]; //TODO: use type information later?
                        $typeRef = $taginfo["_typeReference"];
                        if (!($type == "" || $type == "URL")) {
                            $entities[$tagkey] = $typeRef . "%&" . $type . "%&" . $name;
                            $resources[$name] = $name;
                        }
                    }
                }
            }

            lookUpTagMeanings($socialtags);
            lookUpTagMeanings($resources);

            $root = $usertree->getRoot();
            foreach ($socialtags as $key => $socialtag) {
                $tagtripletrees[$socialtag] = createAutoTag($socialtag);
                $tagtripletrees[$socialtag]->addProperty(SIOC . "has_creator", $root);

                $socialtags[$key] = $tagtripletrees[$socialtag]->getRoot();
            }

            foreach ($entities as $key => $entity) {
                $tagtripletrees[$entity] = createEntityTag($entity);
                $tagtripletrees[$entity]->addProperty(SIOC . "has_creator", $root);
                $tagtripletrees[$entity]->addProperty(OWL. "sameAs", $key);
                $entities[$key] = $tagtripletrees[$entity]->getRoot();
            }

            $usertree->addProperty(FOAF . "interest", $categories);
            $usertree->addProperty(FOAF . "topic", $socialtags);
            $usertree->addProperty(CTAG . "tagged", $entities);
            $usertree->addProperty(FOAF . "primaryTopic", $docid);
            $usertree->addProperty(DCTERMS . "language", $language);
        }
    }
    return $tagtripletrees;
}

function tagTweets(&$tweets, &$usertree, &$timelinetree) {
    $tags = array();
    $tweetstory = "";
    $userprops = $usertree->getProperties();
    $userdescription = $userprops[DCTERMS . 'description'];
    $tagtripletrees = array();

    foreach ($tweets as $tweet) {
        $tweetprops = $tweet->getProperties();
        $tweetstory .= ' ' . $tweetprops[SIOC . 'content'];
    }
    $tweetstory = substr($tweetstory, 0, 9999);

    //echo "TWEETS".PHP_EOL;
    //echo $tweetstory.PHP_EOL.PHP_EOL;
    //Tag user profile
    if ($userdescription != null) {
        $tagtripletrees = array_merge($tagtripletrees, tagUserProfile($usertree));
    }

    //Tag user tweets
    if ($tweetstory != null) {
        $tagsraw = calaisQuery($tweetstory);
        $tagall = json_decode($tagsraw, true);

        $docid = $tagall["doc"]["info"]["docId"];
        $language = $tagall["doc"]["meta"]["language"];

        $socialtags = array();
        $categories = array();
        $entities = array();

        if ($tagall != null) {
            foreach ($tagall as $tagkey => $taginfo) {
                if (array_key_exists("_typeGroup", $taginfo)) {
                    if ($taginfo["_typeGroup"] == "topics") {
                        $name = $taginfo["categoryName"];
                        if (!$name == "")
                            $categories[$name] = $taginfo["category"];
                    }
                    if ($taginfo["_typeGroup"] == "socialTag") {
                        $name = $taginfo["name"];
                        if (!$name == "")
                            $socialtags[$name] = $name;
                    }
                    //Later link entities in text to their connected identifiers such as URL or Company
                    if ($taginfo["_typeGroup"] == "entities") {
                        $name = $taginfo["name"];
                        $type = $taginfo["_type"]; //TODO: use type information later?
                        $typeRef = $taginfo["_typeReference"];
                        if (!($type == "" || $type == "URL")) {
                            $entities[$tagkey] = $typeRef . "%&" . $type . "%&" . $name;
                            $resources[$name] = $name;
                        }
                    }
                }
            }
            lookUpTagMeanings($socialtags);
            lookUpTagMeanings($resources);

            $root = $timelinetree->getRoot();
            foreach ($socialtags as $key => $socialtag) {
                $tagtripletrees[$socialtag] = createAutoTag($socialtag);
                $tagtripletrees[$socialtag]->addProperty(SIOC . "has_container", $root);

                $socialtags[$key] = $tagtripletrees[$socialtag]->getRoot();
            }

            foreach ($entities as $key => $entity) {
                $tagtripletrees[$entity] = createEntityTag($entity);
                $tagtripletrees[$entity]->addProperty(SIOC . "has_container", $root);
                $tagtripletrees[$entity]->addProperty(OWL. "sameAs", $key);
                $entities[$key] = $tagtripletrees[$entity]->getRoot();
            }

            $timelinetree->addProperty(SIOC . "topic", $socialtags);
            $timelinetree->addProperty(CTAG . "tagged", $categories);
            $timelinetree->addProperty(CTAG . "tagged", $entities);
            $timelinetree->addProperty(RDFS . "seeAlso", $docid);
            $timelinetree->addProperty(DCTERMS . "language", $language);
        }
    }
    return $tagtripletrees;
}

function createAutoTag($tag) {
    $tag = ucwords($tag);
    $tag = str_replace(' ', '_', $tag);
    $tagTriple = new TripleTree(TAGS_URI . urlencode($tag));
    $tagTriple->addProperty(RDF . 'type', CTAG . 'AutoTag');
    //$tagTriple->addProperty(RDF . 'type', SIOCT . 'Category');
    $tagTriple->addProperty(CTAG . 'label', $tag);
    $tagMeaning = lookUpTagMeaning($tag);
    if ($tagMeaning)
        $tagTriple->addProperty(CTAG . 'means', $tagMeaning);
    return $tagTriple;
}

function createHashTag($hashtag) {
    $tagTriple = new TripleTree(TAGS_URI . urlencode($hashtag));
    $tagTriple->addProperty(RDF . 'type', CTAG . 'AuthorTag');
    //$tagTriple->addProperty(RDF . 'type', SIOCT . 'Tag');
    $tagTriple->addProperty(CTAG . 'label', $hashtag);
    //$tagMeaning = lookUpHashTag($hashtag);
    //if ($tagMeaning) $tagTriple->addProperty(CTAG.'means', $tagMeaning);
    return $tagTriple;
}

function createEntityTag($entity) {
    $entity = explode("%&", $entity);
    $tagTriple = new TripleTree($entity[0] . '/' . urlencode($entity[2]));
    $tagTriple->addProperty(RDF . 'type', CTAG . 'AutoTag');
    //$tagTriple->addProperty(RDF . 'type', SIOCT . 'Tag');
    //$tagTriple->addProperty(CTAG . 'label', $entity[2] . " is described as " . $entity[1]);
    $tagTriple->addProperty(CTAG . 'label', $entity[2]);
    $tagMeaning = lookUpTagMeaning($entity[2]);
    if (!$tagMeaning)
        $tagMeaning = $entity[0];
    $tagTriple->addProperty(CTAG . 'means', $tagMeaning);
    return $tagTriple;
}

function lookUpHashTag($hashtag) {
    $ch = curl_init('http://api.tagdef.com/' . $hashtag . '.json');
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status_code != 200)
        return lookUpTagMeaning(ucwords($hashtag));
    else {
        $ht = $result;
        $htall = json_decode($ht, true);
        $resultscount = $htall['num_defs'];
        $lastresult = lookUpTagMeaning($htall['defs']['def']['text']);
        if ($resultscount == 1 && $lastresult)
            return $lastresult;
        else
            return 'http://tagdef.com/' . $hashtag;
    }
}

//TODO: Add more meanings to tags
function lookUpTagMeaning($tag) {
    global $tagMeanings;
    $tag = ucwords($tag);
    //in Cache
    if (array_key_exists($tag, $tagMeanings))
        return $tagMeanings[$tag];
    else if (VERIFYTAGDEF) {
        lookUpTagMeaningRequest($tag);
    } else
        return 'http://dbpedia.org/resource/' . urlencode($tag);
}

function lookUpTagMeaningRequest($tag) {
    //on the Net
    $dbp = getFile('http://dbpedia.org/data/' . urlencode($tag) . '.jsod');
    $dbpall = json_decode($dbp, true);
    $resultscount = $dbpall['d']['__count'];
    if ($resultscount == 0)
        return false;
    else
        return 'http://dbpedia.org/resource/' . urlencode($tag);
}

function lookUpTagMeanings($tags) {
    global $tagMeanings;
    if (VERIFYTAGDEF) {
        $urls = array();
        $tagmeanings = array();
        foreach ($tags as $key => $tag) {
            $tag = ucwords($tag);
            $tags[$key] = $tag;
            $urls[$tag] = 'http://dbpedia.org/data/' . urlencode($tag) . '.jsod';
        }
        $meanings = getMultiHttp($urls);
        foreach ($urls as $tag => $url) {
            $meaningall = json_decode($meanings[$url], true);
            $resultscount = $meaningall['d']['__count'];
            if ($resultscount > 0)
                $tagmeanings[$tag] = 'http://dbpedia.org/resource/' . urlencode($tag);
        }
        if ($tagMeanings == null && $tagmeanings != null)
            $tagMeanings = $tagmeanings;
        else if ($tagmeanings != null)
            $tagMeanings = array_merge($tagmeanings, $tagMeanings);
    } else
        return array();
}

?>