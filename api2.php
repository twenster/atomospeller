<?php
require("functions.php");
require("stoichiograph.php");

$wordQuery = $_GET["q"]; // query

if ($wordQuery != null) {
    $db = connectDB();

    $elementsList = getElementsFromDB($db);
    $symbolsList = getSymbolsFromDB($db);
    $processed = array();
    $graphList = array();
    $words = preg_replace('/[\W]+/', '', explode(' ', $wordQuery));

    /*
     Builds an array of all possible spelling for each queried word
    */
    $spellingList = array();
    foreach ($words as $word_key => $word) {
        $graphList[$word_key] = new Graph;
        $spellingList[$word_key] = spell($word_key, $word);
    }

    /*
     Displays spellings with elements

    $spelled_words = array(
        "word 1" = array( // list of suggestions / paths
            0 => array( // suggestion 0 = path 0
                "elemental_word" =>
                "elements" => array(
                    symbol =>
                    number =>
                    url =>
                )
            )
            1 => array( // suggestion 1 = path 1
                ...
            )
        )
        "word 2" => array( // list of suggestions / paths
            ...
        )
    )
    */
    $spelled_words = array();
    foreach ($spellingList as $word_key => $spelling) {

        $spelled_words[ $words[$word_key] ] = array();
        $spelled_words[ $words[$word_key] ]["shortest"] = array(); // shortest paths
        $spelled_words[ $words[$word_key] ]["all"] = array(); // list of all paths
        $shortest_length = strlen($words[$word_key]);

        foreach ($spelling as $path_id => $paths) {
            $spelled_words[ $words[$word_key] ]["all"][$path_id] = array(); // path #x
            $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] = ""; // elements imploded
            $spelled_words[ $words[$word_key] ]["all"][$path_id]["symbols"] = array(); // emenets in array

            foreach ($paths as $node) {
                $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] = $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] . ucfirst($graphList[$word_key]->nodes[$node][0]);
                $spelled_words[ $words[$word_key] ]["all"][$path_id]["symbols"][] = ucfirst($graphList[$word_key]->nodes[$node][0]);
            }

            // saving the shortest word found
            if (count($paths) < $shortest_length) {
                $shortest_length = count($paths);
                $spelled_words[ $words[$word_key] ]["shortest"][0] = $spelled_words[ $words[$word_key] ]["all"][$path_id];
            }
        }
    }
    $spelled_words_json = json_encode($spelled_words);

    header('Content-Type: application/json');
    print($spelled_words_json);
}
?>
