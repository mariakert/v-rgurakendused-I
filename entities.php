<?php 
//call this function 

function DoHTMLEntities ($string) { 
    $trans_tbl[chr(145)] = '&#8216;'; 
    $trans_tbl[chr(146)] = '&#8217;'; 
    $trans_tbl[chr(147)] = '&#8220;'; 
    $trans_tbl[chr(148)] = '&#8221;'; 
    $trans_tbl[chr(142)] = '&eacute;'; 
    $trans_tbl[chr(150)] = '&#8211;'; 
    $trans_tbl[chr(151)] = '&#8212;'; 
    return strtr ($string, $trans_tbl); 
} 

//insert your string variable here 
function toHTMLEntities($string) {
    $foo = str_replace("\r\n\r\n","", htmlentities($string)); 
    $foo2 = str_replace("\r\n"," ", $foo); 
    $foo3 = str_replace(" & ","&amp;", $foo2); 
    return DoHTMLEntities($foo3);
}
?>