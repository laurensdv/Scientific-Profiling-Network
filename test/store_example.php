<?php
include_once("../interlinking/semsol-arc/ARC2.php");
require_once '../config/configuration.php';

$config = array(
  /* db */
  'db_host' => SOCKET,
  'db_name' => RDF_DB,
  'db_user' => USER,
  'db_pwd' => PASS,
  /* store */
  'store_name' => 'arc_tests',
  /* stop after 100 errors */
  'max_errors' => 100,
);
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  $store->setUp();
}

/* LOAD will call the Web reader, which will call the
format detector, which in turn triggers the inclusion of an
appropriate parser, etc. until the triples end up in the store. */
$store->query('LOAD <http://example.com/home.html>');

/* list names */
$q = '
  PREFIX foaf: <http://xmlns.com/foaf/0.1/> .
  SELECT ?person ?name WHERE {
    ?person a foaf:Person ; foaf:name ?name .
  }
';
$r = '';
if ($rows = $store->query($q, 'rows')) {
  foreach ($rows as $row) {
    $r .= '<li>' . $row['name'] . '</li>';
  }
}

echo $r ? '<ul>' . $r . '</ul>' : 'no named persons found';
?>