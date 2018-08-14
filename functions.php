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
//    return array_map('current', $db->query('SELECT symbol, name, number FROM elements')->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)); 
    return $db->query('SELECT symbol, name, number FROM elements')->fetchAll(); 
} 

function getSymbolsFromDB($db) { 
    return $db->query('SELECT symbol FROM elements')->fetchAll(PDO::FETCH_COLUMN); 
} 

// setter

// Functions

/*
 * $found =
 * 0 : aucune occurence trouvée
 * 1 : occurence trouvée
 * 2 : fin de recherche (tail est vide)
*/
function searchElementFromHead($word, &$elementList, $len, &$foundElementArray) {
    global $lengthPref;
    // si le mot est vide, bye
//print("tail:'".$word."'<br>\n");
    if($word=="") 
        return 2;

    $found = 0;

    // on prend le nombre de caractère de pref.
    // si le caratère existe comme élément, on ajoute l'élément dans le tableau et on boucle avec le reste (tail)

    $head = substr($word, 0, $len);
//print("1: ".$len.", ".$head." ");
    if (in_arrayi($head, $elementList)) {
//print("found<br>\n");
        $found = 1;
        array_push($foundElementArray, getElement($head, $elementList));
        $tail = substr($word, $len);
        $found = searchElementFromHead($tail, $elementList, $lengthPref, $foundElementArray);
    }

    if ($found==0) {
        $len = ( ($len==1)?2:1 );
        $head = substr($word, 0, $len);
//print("2: ".$len.", ".$head." ");
        if (in_arrayi($head, $elementList)) {
//print("found<br>\n");
            $found = 1;
            array_push($foundElementArray, getElement($head, $elementList));
            $tail = substr($word, $len);
            $found = searchElementFromHead($tail, $elementList, $len, $foundElementArray);
        }
    }
    return $found;
}

function in_arrayi($needle, &$haystack)
{
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function array_searchi($needle, &$haystack)
{
    return array_search(strtolower($needle), array_map('strtolower', $haystack));
}

?>
