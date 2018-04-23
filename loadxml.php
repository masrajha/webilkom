<?php
function loadXMLToArray($path){

//read entire file into string
$xmlfile = file_get_contents($path);

//convert xml string into an object
$xml = simplexml_load_string($xmlfile);

//convert into json
$json  = json_encode($xml);

//convert into associative array
$xmlArr = json_decode($json, true);
return $xmlArr;
}
print_r(loadXMLToArray("http://localhost/latihan/kur_ilkom_2005.xml"));