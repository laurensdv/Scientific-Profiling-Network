<?php
/*
USE EXAMPLE

$DYM = new DYM();
$DYM->lang = 'en-US';

//IF TRUE, DISPLAY AN CORRECT GOOGLE ALTERNATIVE
if($DYM->search('coreqt')){
   echo $DYM->correct;
}
*/
class DYM{

      private $query;

      public $lang = 'en',
             //SEARCH URL, PLEASE, FOR UPDATES, DON'T REPLACE '#LANGUAGE#' and '#QUERY#'
             $search_addr = 'http://www.google.com/search?hl=#LANGUAGE#&q=#QUERY#&meta=',
             //RETURN OF CORRECT ALTERNATIVE
             $correct;

      public function __construct(){

      }
      
      public function buildURL($query) {
          $this->query = $query;
          $url = str_replace(array('#LANGUAGE#','#QUERY#'),array($this->lang,urlencode($this->query)),$this->search_addr);
          return $url;
}
      public function searchMulti($urls){
             if (!is_array($urls)) return null;
             //MAKE ADDRESS
             $resultatados = getMultiHttp($urls);
             $results = array();
             if (!is_array($resultatados) || $resultatados == null) return null;
             foreach($resultatados as $key => $resultado) {
             // initialize DOM
             $doc = new DOMDocument();
             @$doc->loadHTML($resultado);

             try{
                 $aTag = $doc->getElementByID('topstuff')->getElementsByTagName('a');

                foreach($aTag as $object){
                     if($object->getAttribute('class') == 'spell'){
                         //echo $key." -> ".$object->nodeValue.PHP_EOL;
                        $results[$key] = $object->nodeValue;
                     } else $results[$key] = $key;
                   }
                }
              catch(Exception $e) {
                 $e.error_log($key. " not done");
             }
            }
             return $results;
      }
      public function search($query){
             $this->query = $query;
             //MAKE ADDRESS
             $resultado = file_get_contents(str_replace(array('#LANGUAGE#','#QUERY#'),array($this->lang,urlencode($this->query)),$this->search_addr));

             // initialize DOM
             $doc = new DOMDocument();
             @$doc->loadHTML($resultado);

             $aTag = $doc->getElementByID('topstuff')->getElementsByTagName('a');
             foreach($aTag as $object){
                     if($object->getAttribute('class') == 'spell'){
                        $this->correct = $object->nodeValue;
                        return true;
                     }
             }

             //RETURNS
             return false;
      }

}

?> 