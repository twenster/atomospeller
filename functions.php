<?php
/* Global

 */

// Variables
require("config.php");

// DB Connect

//  Create a new connection to the MySQL database using PDO
function connectDB() {
    global $servername, $database, $username, $password;
    return new PDO('mysql:host='.$servername.';dbname='.$database, $username, $password);
 }


// getter
function getElement($string, &$array) {
    foreach( $array as $item) {
        if( strtolower($item) == strtolower($string))
            return $item;
    }
}

function getElementsFromDB($db) {
    $result = $db->query('SELECT symbol, name, number FROM elements')->fetchAll();
    array_unshift($result, "dummy");
    return $result;
}

function getSymbolsFromDB($db) {
    $result = $db->query('SELECT symbol FROM elements')->fetchAll(PDO::FETCH_COLUMN);
    array_unshift($result, "dummy");
    return $result;
}

// setter

// Functions

/*
 * $word = word or tail of the word to search
 * $lengthPref = search a 2 character or 1 character length element
 * $endOfWord or return = true when there is the word or tail is empty, we stop the search.
 * $head = 1 or 2 charecter element to search in the element list
 * $tail = the end part of the word we still need to look for.
 */
function searchElement($word, $lengthPref) {
    global $foundElementArray, $symbolsList;
    $endOfWord = false;

    // search for $lengPref characters element
    $head = substr($word, 0, $lengthPref);
    if (strlen($head)==0)
        return true;

    if (in_arrayi($head, $symbolsList)) {

        array_push($foundElementArray, getElement($head, $symbolsList));
        $tail = substr($word, $lengthPref);
        $endOfWord = searchElement($tail, $lengthPref);
        if ($endOfWord == true)
            return true;
        return false;
    }

    // swap length
    $lengthPref = ( ($lengthPref==1) ? 2 : 1 );

    // 1 character element
    $head = substr($word, 0, $lengthPref);
    if (strlen($head)==0)
        return true;

    if (in_arrayi($head, $symbolsList)) {

        array_push($foundElementArray, getElement($head, $symbolsList));
        $tail = substr($word, $lengthPref);
        $endOfWord = searchElement($tail, $lengthPref);
        if ($endOfWord == true)
            return true;
        return false;
    }

    // no element found, we loop to the next character, ad forget the $head
    $tail = substr($word, 1);
    $endOfWord = searchElement($tail, 1);
    if ($endOfWord == true)
        return true;
    return false;

}


function in_arrayi($needle, &$haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function array_searchi($needle, &$haystack) {
    return array_search(strtolower($needle), array_map('strtolower', $haystack));
}

?>
