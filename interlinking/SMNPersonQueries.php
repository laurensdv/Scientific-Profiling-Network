<?php
/**
 * Description of SMNPersonQueries
 *
 * @author laurens
 */
class SMNPersonQueries {
    public function accounts($identifier) {
        $query = 'SELECT ?z
                            WHERE  {
                            ?z ?property "' . $identifier . '" .
                            ?z rdf:type sioc:UserAccount
                            }';
        $res = SMNStore::query($query);
        return SMNStore::cleanUpSingleResult($res);
    }
}
?>
