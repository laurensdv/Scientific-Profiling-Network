<?php
/* ARC2 static class inclusion */
require_once('configuration.php');

function getConfigArc() {
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
    'select', 'construct', 'ask', 'describe', 
    'load', 'insert', 'delete', 
    'dump' /* dump is a special command for streaming SPOG export */
  ),
  'endpoint_timeout' => 0, /* not implemented in ARC2 preview */
  'endpoint_read_key' => '', /* optional */
  'endpoint_write_key' => '', /* optional */
  'endpoint_max_limit' => 250, /* optional */
);
return $config_arc;
}
?>