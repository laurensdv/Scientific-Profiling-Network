<?php
class TripleTree {
	protected $properties;
	protected $root;
	
	function __construct($root) {
		$this->root = $root;
		$this->properties = array();
	}
	
   /*
    *  Searches for the first occurence of the given argument
    **/
   public function indexOf($object)
   {
          if(($index =array_search($object,$this->properties)) !==false)
              return $index;
          else
              return -1;
   }
   
   /*
    * This function will retain TRUE if $object is contained
    * within the vector else FALSE
    */
   
   public function contains($object)
   {
           return ($this->indexOf($object)>=0);
   }
	
	function addProperty($predicate,$object) {
		$this->properties[$predicate]=$object;
	}
	
	function removeProperty($predicate) {
		unset($this->properties[$predicate]);
	}
	
	function getProperties() {
		return $this->properties;
	}
	
	function getRoot() {
		return $this->root;
	}
}
?>