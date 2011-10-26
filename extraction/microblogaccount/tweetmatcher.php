<?php
function matchUrls(&$tweet, $tweetcontent) {
    $objects = performGrep('`[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?`', $tweetcontent);
    //$objects=performGrep('`(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/[a-zA-Z0-9\.\,\?\'\\\+&%\$#\=~_\-]*)*`',$tweetcontent);
    $objects = $objects[0];
    foreach ($objects as $key => $object) {
        $pos = strpos($object, 'http://');
        if ($pos == false) {
            $pos = strpos($object, '://') + 3;
            $object = substr($object, $pos);
            $objects[$key] = 'http://' . $object;
        } else
            $objects[$key] = substr($object, $pos);
    }
    //print_r($objects[0]);
    //$objects=preg_grep('`(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/[a-zA-Z0-9\.\,\?\'\\\+&%\$#\=~_\-]*)*`',$objects);
    if (count($objects) > 0)
        $tweet->addProperty(SIOC . 'links_to', $objects);
}

function matchMentions(&$tweet, $tweetcontent) {
    $objects = performGrep('/@([a-zA-Z0-9_]{1,20})/', $tweetcontent);
    $objects = preg_grep('/^[a-zA-Z0-9_]([a-zA-Z0-9_]{1,20})/', $objects[1]);
    //print_r($objects);
    if (count($objects) > 0)
        $tweet->addProperty(SIOC . 'addressed_to', $objects);
}

function matchHashTags(&$tweet, $tweetcontent) {
    $hashtags = array();
    $objects = performGrep('/#([a-zA-Z0-9_]+)/', $tweetcontent);
    $objects = preg_grep('/^[a-zA-Z0-9_]([a-zA-Z0-9_]+)/', $objects[1]);
    $hashtaguris = array();
    $tagmeanings = array();
    $meanings = array();
    if (count($objects) > 0) {
        if (VERIFYTAGDEF) {
            foreach ($object as $object) {
                 $hashtaguris[$object] = 'http://dbpedia.org/data/' . urlencode(ucwords($tag)) . '.jsod';
            }
            $meanings = getMultiHttp($hashtaguris);

            foreach ($hashtaguris as $tag => $url) {
                $meaningall = json_decode($meanings[$url], true);
                $resultscount = $meaningall['d']['__count'];
                if ($resultscount > 0)
                    $tagmeanings[$tag] = 'http://dbpedia.org/resource/' . urlencode(ucwords($tag));
            }
        }
        foreach ($objects as $key => $object) {
            $hashtags[$object] = createHashTag($object);
            $objects[$key] = $hashtags[$object]->getRoot();
            $hashtags[$object]->addProperty(SIOC . 'has_container', $tweet->getRoot());
            if (array_key_exists($object, $tagmeanings))
                $hashtags[$object]->addProperty(CTAG . 'means', $tagmeanings[$object]);
            else {
                if(VERIFYTAGDEF) $tagMeaning = lookUpHashTag($object);
                    else $tagMeaning = 'http://dbpedia.org/resource/' . urlencode(ucwords($object));
                //if ($tagMeaning || !VERIFYTAGDEF) //add dbpedia by default - not useful!
                //    $hashtags[$object]->addProperty(CTAG . 'means', $tagMeaning);
            }
        }
        $tweet->addProperty(SIOC . 'tagged', $objects);
    }
    return $hashtags;
}

function performGrep($regexp, $tweetcontent) {
    preg_match_all($regexp, $tweetcontent, $matches);

    return $matches;
}

?>