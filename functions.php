<?php
/* Global

 */

// Variables
require("config.php");

// DB Connect
$db = connectDB();
//$dbElementsList = getElementsFromDB($db);
//$dbSymbolsList = getSymbolsFromDB($db);

//  Create a new connection to the MySQL database using PDO
function connectDB() {
    //global $servername, $database, $username, $password;
    return new PDO('mysql:host='.DBSERVERNAME.';dbname='.DBDATABASE, DBUSERNAME, DBPASSWORD);
 }


// getter
function getElement($string, &$array) {
    foreach( $array as $item) {
        if( strtolower($item) == strtolower($string))
            return $item;
    }
}

function getElementsFromDB() {
    global $db;
    $result = $db->query('SELECT symbol, name, number FROM elements')->fetchAll();
    array_unshift($result, "dummy");
    return $result;
}

function getSymbolsFromDB() {
    global $db;
    $result = $db->query('SELECT symbol FROM elements')->fetchAll(PDO::FETCH_COLUMN);
    array_unshift($result, "dummy");
    return $result;
}

function getStatsbyWordCount() {
    global $db;
    $result = $db->query('SELECT count(word) as word_count, word FROM `search_log` WHERE 1 GROUP BY word ORDER BY count(word) DESC')->fetchAll();
    return $result;
}

function getStatsbyDate() {
    global $db;
    $result = $db->query('SELECT timestamp as word_date, word FROM `search_log` WHERE 1 ORDER BY timestamp DESC LIMIT 50')->fetchAll();
    return $result;
}
// setter

function setStats($word) {
    global $db;
    $result = $db->prepare( "INSERT INTO search_log SET timestamp = NOW(), word = '" . $word . "'" )->execute();
}
// Functions
function in_arrayi($needle, &$haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function array_searchi($needle, &$haystack) {
    return array_search(strtolower($needle), array_map('strtolower', $haystack));
}

?>
