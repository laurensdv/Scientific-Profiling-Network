<?php
//CONFIGURATION

//Socket to connect to
define("SOCKET", ":/Applications/MAMP/tmp/mysql/mysql.sock");
define("GRAB_SOCKET", SOCKET);
//Database name
define("DB", "test");
define("RDF_DB", "semant11_rdf1");
define("COLINDA_DB", "semant11_colinda");
//Credentials
define("USER", "root");
define("PASS", "root");
//Credentials
define("GRAB_USER", "root");
define("GRAB_PASS", "root");

define("USE_GRABEETER",false);

define("STORENAME",'arc_testing');

define("TWEETS_LIMIT",1000);
define("FRIENDS_LIMIT",250);

//DEBUG Mode
define("DEBUG", true);

//ROOT
define("URI","http://localhost:8888/");
define("ROOT",URI."rdf");

//Verify tag meanings
define("VERIFYTAGDEF",false);

//Loadsize
define("LOADSIZE",150);

//Explicit annotation
//TODO: big issue
define("EXPLICIT",false);

//Tag params
define("DOSPELLCHECK",false);
define("DOWORDNET",false);
//
?>