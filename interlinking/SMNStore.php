<?php

/**
 * Description of SMNStore
 *
 * @author laurens
 */
class SMNStore {

    var $store;

    function updateQuery($query) {
        $upd_query = "
	PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX sioc_t: <http://rdfs.org/sioc/types#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX tags: <http://www.holygoat.co.uk/owl/redwood/0.1/tags/>
PREFIX moat: <http://moat-project.org/ns#>
PREFIX opo: <http://online-presence.net/opo/ns#>
PREFIX opo-actions: <http://online-presence.net/opo-actions/ns#>
PREFIX ctag: <http://commontag.org/ns#>
PREFIX smob: <http://smob.me/ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX rev: <http://purl.org/stuff/rev#>
PREFIX wgs84_pos: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX gn: <http://www.geonames.org/ontology#>
PREFIX dbp: <http://dbpedia.org/ontology/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
        PREFIX swrc: <http://swrc.ontoware.org/ontology#>
		$query";
        return $upd_query;
    }
    function ask($query) {
        return SMNStore::query("ASK { $query }", true);
    }

    function describe($uri) {
        return SMNStore::query("DESCRIBE <$uri>", true);
    }

    function query($query, $ask=false) {
        $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $query = "
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX sioc_t: <http://rdfs.org/sioc/types#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX tags: <http://www.holygoat.co.uk/owl/redwood/0.1/tags/>
PREFIX moat: <http://moat-project.org/ns#>
PREFIX opo: <http://online-presence.net/opo/ns#>
PREFIX opo-actions: <http://online-presence.net/opo-actions/ns#>
PREFIX ctag: <http://commontag.org/ns#>
PREFIX smob: <http://smob.me/ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX rev: <http://purl.org/stuff/rev#>
PREFIX wgs84_pos: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX gn: <http://www.geonames.org/ontology#>
PREFIX dbp: <http://dbpedia.org/ontology/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
        PREFIX swrc: <http://swrc.ontoware.org/ontology#>
		$query";

        $rs = $store->query($query);

        if ($errors = $store->getErrors()) {
            error_log("SMOB SPARQL Error:\n" . join("\n", $errors));
            return array();
        }

        if ($ask) {
            return $rs['result'];
        } else if (count($rs['result'])>0)
            return $rs['result']['rows'];
        else return false;
    }

    public function doPing($title, $URI) {
        $client = new xmlrpc_client("http://sindice.com/xmlrpc/api");
        $payload = new xmlrpcmsg("weblogUpdates.ping");

        $payload->addParam(new xmlrpcval($title));
        $payload->addParam(new xmlrpcval($URI));

        $client->setDebug(2);

        $response = $client->send($payload);
        $xmlresponsestr = $response->serialize();

        $xml = simplexml_load_string($xmlresponsestr);
        $result = $xml->xpath("//value/boolean/text()");
        if ($result) {
            if ($result[0] == "0") {
                echo "<p>Submitting $URI to $servicetitle succeeded.</p>";
                return;
            }
        } else {
            $err = "Error Code: "
                    . $response->faultCode()
                    . "<br /> Error Message: "
                    . $response->faultString();
            echo "<p>Failed to submit $URI to $servicetitle.</p>";
        }
    }

    public function getConfigArc() {
        /* configuration */
        $config_arc = array(
            /* db */
            'db_host' => SOCKET, /* optional, default is localhost */
            'db_name' => RDF_DB,
            'db_user' => USER,
            'db_pwd' => PASS,
            /* store name (= table prefix) */
            'store_name' => STORENAME,
            /* stop after 100 errors */
            'max_errors' => 100,
            'store_write_buffer' => 10000,
            /* endpoint */
            'endpoint_features' => array(
                'select', 'construct', 'ask', 'describe', 'load', 'insert','delete',
                'dump' /* dump is a special command for streaming SPOG export */
            ),
            'endpoint_timeout' => 0, /* not implemented in ARC2 preview */
            'endpoint_read_key' => '', /* optional */
            'endpoint_write_key' => '', /* optional */
            'endpoint_max_limit' => 250, /* optional */
        );
        return $config_arc;
    }
    public function removeProperty($subject,$predicate) {
        $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $q = 'DELETE { '.$subject.' '.$predicate.' ?o }';

        $store->query($q, 'raw', '', true);
    }
    public function storeTriple($subject,$predicate,$object) {
        $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $q2 = "INSERT INTO <".ROOT."> { ".$subject.' '.$predicate.' '.$object.' }';

        $q2 = SMNStore::updateQuery($q2);

        $store->query($q2, 'raw', '', true);
    }
    public function storeTripleNoPrefix($subject,$predicate,$object) {
        $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $q2 = "INSERT INTO <".ROOT."> { ".$subject.' '.$predicate.' '.$object.' }';

        $store->query($q2, 'raw', '', true);
    }
    public function storeTriplesNoPrefix($string) {
                $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $q2 = 'INSERT INTO <'.ROOT.'> { '.$string.' }';

        $store->query($q2, 'raw', '', true);
    }
    public function updateTriple($subject,$predicate,$object) {
        $arc_config = SMNStore::getConfigArc();

        $store = ARC2::getStore(SMNStore::getConfigArc());
        if (!$store->isSetUp()) {
            $store->setUp();
        }

        $q1 = 'DELETE { '.$subject.' '.$predicate.' ?o }';
        $q2 = "INSERT INTO <".ROOT."> { ".$subject.' '.$predicate.' '.$object.' }';

        $q1 = SMNStore::updateQuery($q1);
        $q2 = SMNStore::updateQuery($q2);
        
        $store->query($q1, 'raw', '', true);
        $store->query($q2, 'raw', '', true);
    }

    public function cleanUpSingleResult($triples) {
        $cleaned = array();
        foreach ($triples as $triple) {
            $cleaned[] = $triple['z'];
        }
        return $cleaned;
    }

}

?>
