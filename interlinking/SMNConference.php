<?php
/**
 * Description of SMNConference
 *
 * @author laurens
 */

class SMNConference {
    private $conferencedata = array();

    public function  __construct() {
        $link = SMNConference::connect();

	$sql = "SELECT title FROM conferences LIMIT 15000";

	$result = mysql_query($sql, $link);

	if (!$result) {
		echo "DB Error, could not query the database\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}
        $row = array();

	while ($row = mysql_fetch_assoc($result)) {
                $this->conferencedata[] = $row['title'];
        }
    }

    //put your code here
    public function connect() {
        $link = mysql_connect(SOCKET, USER, PASS);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}

	if (!mysql_select_db(COLINDA_DB, $link)) {
		echo 'Could not select database';
		exit;
	}
        return $link;
    }

    public function findConference($conferencetag) {
        $link = SMNConference::connect();
        if(is_numeric(substr($conferencetag, -4)))
        $title = substr($conferencetag, 0, strlen($conferencetag)-4).'%'.  substr($conferencetag, -4);
        else $title = substr($conferencetag, 0, strlen($conferencetag)-2).'%'.  substr($conferencetag, -2);

	$sql = sprintf("SELECT * FROM conferences WHERE title LIKE '%s'",
	mysql_real_escape_string($title));

	$result = mysql_query($sql, $link);

	if (!$result) {
		echo "DB Error, could not query the database\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}
        $row = array();
        $conferencedata = null;
	while ($row = mysql_fetch_assoc($result)) {
                $conferencedata = $row;
        }
        return $conferencedata;
    }

    public function isConferenceFast($conferencetag) {
        if(is_numeric(substr($conferencetag, -4)))
        $title = substr($conferencetag, 0, strlen($conferencetag)-4).' '.  substr($conferencetag, -4);
        else $title = substr($conferencetag, 0, strlen($conferencetag)-2).' '.  substr($conferencetag, -2);
        if(!array_search($title, $this->conferencedata)) return true;
            else return false;
         if(!array_search($conferencetag, $this->conferencedata)) return false;
            else return true;

     }
     public function fixDate($conferencetag) {
        if(is_numeric(substr($conferencetag, -4)))
            $title = $conferencetag;
        else $title = substr($conferencetag, 0, strlen($conferencetag)-2).'20'.  substr($conferencetag, -2);
        return $title;
     }
    public function findConferencesColinda($conferencetags) {
         $urls = array();
         foreach($conferencetags as $conferencetag) {
             $urls[$conferencetag] = 'http://data.colinda.org/endpoint.php?query=PREFIX+swrc:+%3Chttp://swrc.ontoware.org/ontology%23%3E%0D%0APREFIX+gn:+++%3Chttp://www.geonames.org/ontology%23%3E%0D%0APREFIX+rdf:++%3Chttp://www.w3.org/1999/02/22-rdf-syntax-ns%23%3E%0D%0APREFIX+geo:++%3Chttp://www.w3.org/2003/01/geo/wgs84_pos%23%3E%0D%0APREFIX+rdfs:+%3Chttp://www.w3.org/2000/01/rdf-schema%23%3E%0D%0APREFIX+owl:++%3Chttp://www.w3.org/2002/07/owl%23%3E%0D%0A%0D%0ADESCRIBE+%3Fs+where+{+%3Fs+rdfs:label+%22'.urlencode(strtoupper(self::fixDate($conferencetag))).'%22+}&output=json&jsonp=&key=';
         }
         return getMultiHttp($urls);
    }
    public function isConference($conferencetag) {
        return !SMNConference::findConference($conferencetag)==null;
    }
}
?>
