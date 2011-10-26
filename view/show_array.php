<?php
function do_offset($level){
    $offset = "";             // offset for subarry 
    for ($i=1; $i<$level;$i++){
    $offset = $offset . "<td></td>";
    }
    return $offset;
}

function show_array($array, $level, $sub){
    if (is_array($array) == 1){          // check if input is an array
       echo '<tr>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>';
       foreach($array as $key_val => $value) {
           $offset = "";
           if (is_array($value) == 1){   // array is multidimensional
           echo "<tr>";
           $offset = do_offset($level);
           echo $offset . "<td>" . $key_val . "</td>";
           show_array($value, $level+1, 1);
           }
           else{                        // (sub)array is not multidim
           if ($sub != 1){          // first entry for subarray
              
               echo "<tr nosub>";
               $offset = do_offset($level);
           }
           $sub = 0;
           echo $offset . "<td main ".$sub." height = \"16\" width=\"16\">" . $key_val .
               "</td><td class=\"style0\" height = \"16\" width=\"16\">" . $value . "</td>";
           echo "</tr>\n";
           }
       } //foreach $array
    }  
    else{ // argument $array is not an array
        return;
    }
}

function html_show_array($array){
  echo "<table class=\"listing\" cellspacing=\"0\" border=\"2\">\n";
  show_array($array, 1, 0);
  echo "</table>\n";
}
?>