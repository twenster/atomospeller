<?php
require 'functions.php';
require 'stoichiograph.php';

// Arguments
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
switch ($method) {
    case 'POST':
        $getArgs = filter_input_array(INPUT_POST); // application/x-www-form-urlencoded
        break;

    case 'GET':
        $getArgs = filter_input_array(INPUT_GET); // application/x-www-form-urlencoded
        break;
}
if ($getArgs == "") { // application/json
    $getArgs = json_decode(file_get_contents("php://input"), TRUE);
}

// srch.php?q=nice
$wordList = $getArgs["q"];

if ( ($wordList != null) || ($wordList !='') ) {
    $spelled_words_json = doSpell($wordList);
} else {
    $spelled_words_json = "{\"status\":\"false\", \"description\":\"query is empty\"}";
}
//$spelled_words_json = "{\"status\":\"".$_GET["q"]."\"}";
displaySpelledWords($spelled_words_json);

?>
