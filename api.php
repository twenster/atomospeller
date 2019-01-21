<?php
require 'functions.php';
require 'stoichiograph.php';
require 'foundry.php';
//require'stats.php';

// very old
//$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
// old
//$method = $_SERVER['REQUEST_METHOD'];
// XXI century
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

switch ($method) {
    case 'PUT':
        //do_something_with_put($request);
        break;

    case 'POST':
        $postArgs = filter_input_array(INPUT_POST); // application/x-www-form-urlencoded
        if ($postArgs == "") { // application/json
            $postArgs = json_decode(file_get_contents("php://input"), TRUE);
        }
        switchTab($postArgs);
        break;

    case 'GET':
        $getArgs = filter_input_array(INPUT_GET); // application/x-www-form-urlencoded
        if ($getArgs == "") { // application/json
            $getArgs = json_decode(file_get_contents("php://input"), TRUE);
        }
        switchTab($getArgs);
        break;

    default:
        //handle_error($request);
        break;
}

function switchTab($formArgs) {
    switch ($formArgs["t"]) { // t = tab, function
        case 'search':
            doSearch($formArgs["q"]);
            break;

        case 'image':
            doFoundry($formArgs["s"], $formArgs["w"], $formArgs["d"]);
            break;

        /*case 'stats':
            doStat();
            break;
        */
        default:
            break;
    }

}

function doSearch($wordQuery) {
    if ( ($wordQuery != null) || ($wordQuery !='') ) {
        $spelled_words_json = doSpell($wordQuery);
        displaySpelledWords($spelled_words_json);
    }
}

function doFoundry($symbolList, $elementWidth, $wordDisplay) {
    if ($symbolList != null) {
        $wordCanvas = composeImage($symbolList, $elementWidth, $wordDisplay);
        displayImage($symbolList, $wordCanvas);
    }
}
?>
