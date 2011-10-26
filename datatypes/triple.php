<?php
class Triple
{
	protected $subject;
	protected $predicate;
	protected $object;

    /**Full triple Constructor*/
    function __construct($subj=null, $obj=null, $pred=null)
    {
        $this->subject = $subj;
        $this->predicate = $obj;
        $this->object = $pred;
    }
    
    public function changeSubject($subj) {
    	$this->subject = $subj;
    }
    
    public function changePredicate($pred) {
    	$this->predicate = $pred;
    }
    
    public function changeObject($obj) {
    	$this->object = $obj;
    }
    
    /*
    *  Searches for the first occurence of the given argument
    **/
   private function indexOf($item)
   {
          if($this->subject==$item) return 0;
          if($this->predicate==$item) return 1;
          if($this->object==$item) return 2;
          return -1;
   }
   
    
   /*
    * This function will retain TRUE if $object is contained
    * within the triple else FALSE
    */
   
   public function contains($object)
   {
        return ($this->indexOf($object)>=0);
   }
   
   public function getProperty()
   {
   		return array($this->predicate => $this->object);
   }
   
   public function getSubject()
   {
   		return $this->subject;
   }
   
   public function getPredicate()
   {
   		return $this->predicate;
   }
   
   public function getObject()
   {
   		return $this->object;
   }
   
   function __toString() {
        $desc  = "<strong>".$this->subject."</strong> ---(";
        $desc .= $this->predicate.")--> <em>";
        $desc .= $this->object."</em><br />";
        return $desc;
    }
}