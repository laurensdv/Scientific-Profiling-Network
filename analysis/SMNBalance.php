<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNBalance
 *
 * @author laurens
 */
class SMNBalance {
    //rank a resultset
    public function rank($array) {
        $ranking = array();
        $ranking = array_count_values($array);
        arsort($ranking,SORT_NUMERIC);
        return $ranking;
    }
    //rate a uri based on the number of properties it has
    public function rate($array) {
        
    }
    public function mergeRankings() {
        
    //check if there was at least one argument passed. 
    if(func_num_args() > 0){ 
        //get all the arguments 
        $args = func_get_args(); 
        //get the first argument 
        $array = array_shift($args); 
        //check if the first argument is not an array 
        //and if not turn it into one. 
        if(!is_array($array)) $array = array($array); 
        //loop through the rest of the arguments.
        $i=0;
        foreach($args as $array2){ 
            //check if the current argument from the loop 
            //is an array. 
            if(is_array($array2)){ 
                //if so then loop through each value. 
                foreach($array2 as $k=>$v){ 
                    //check if that key already exists. 
                    if(isset($array[$k])){ 
                        //check if that value is already an array. 
                        if(is_array($array[$k])){ 
                            //if so then add the value to the end 
                            //of the array. 
                            $array[$k][$i] = $v;
                        } else { 
                            //if not then make it one with the 
                            //current value and the new value. 
                            $array[$k] = array($array[$k], $v); 
                        } 
                    } else { 
                        //if not exist then add it 
                        $array[$k] = $v; 
                    } 
                } 
            } else { 
                //if not an array then just add that value to 
                //the end of the array 
                $array[$i] = $array2;
            }
            $i++;
        } 
        //return our array. 
        return($array); 
    } 
    //return false if no values passed. 
    return(false); 
    }
}
?>
