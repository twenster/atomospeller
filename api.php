<?php
require("functions.php");
require("stoichiograph.php");
require("foundry.php");

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
        $postArgs = filter_input_array(INPUT_POST);
        switchTab($postArgs);
        break;

    case 'GET':
        $getArgs = filter_input_array(INPUT_GET);
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
            doFoundry($formArgs["s"], $formArgs["w"]);
            break;

        default:
            break;
    }

}

function doSearch($wordQuery) {
    if ( ($wordQuery != null) || ($wordQuery !='') )
        spellWord($wordQuery);
}

function doFoundry($symbolList, $symbolHeight) {
    if ($symbolList != null) {
        $symbolList = json_decode( $symbolList );
        if ( ($symbolHeight == 0) || ($symbolHeight == "") )
            $symbolHeight = 128;
        composeImage($symbolList, $symbolHeight);
    }
}
?>
