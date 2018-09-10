<?php
require("functions.php");
require("stoichiograph.php");

$wordQuery = $_GET["q"]; // query
//$length = $_GET["l"]; // length (all, shortest)

if ($wordQuery != null) {
    $db = connectDB();

    $elementsList = getElementsFromDB($db);
    $symbolsList = getSymbolsFromDB($db);
    $processed = array();
    $graphList = array();
    $words = preg_replace('/[\W]+/', '', explode(' ', $wordQuery));

    /* Builds an array of all possible spelling for each queried word
    */
    $spellingList = array();
    foreach ($words as $word_key => $word) {
//print "word: ".$word."<br>\n";
        $graphList[$word_key] = new Graph;
        $spellingList[$word_key] = spell($word_key, $word);
    }

    /* Displays spellings with elements

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

//print("Spellings:<br>\n");
//print_r($spelling);
//print("word: ".$words[$word_key]."<br>\n");
        $spelled_words[ $words[$word_key] ] = array(); // list of paths
        $spelled_words[ $words[$word_key] ]["shortest"] = array(); // shortest paths
        $spelled_words[ $words[$word_key] ]["all"] = array(); // list of paths
        $shortest_length = strlen($words[$word_key]);

        foreach ($spelling as $path_id => $paths) {
            $spelled_words[ $words[$word_key] ]["all"][$path_id] = array(); // path x
            $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] = "";
            $spelled_words[ $words[$word_key] ]["all"][$path_id]["elements"] = array();

            //$spelled_words[ $words[$word_key] ]["shortest"][$path_id] = array(); // path x
            //$spelled_words[ $words[$word_key] ]["shortest"][$path_id]["elemental_word"] = "";
            //$spelled_words[ $words[$word_key] ]["shortest"][$path_id]["elements"] = array();

            foreach ($paths as $node) {
//print("node: ".$node."<br>");
                $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] = $spelled_words[ $words[$word_key] ]["all"][$path_id]["elemental_word"] . ucfirst($graphList[$word_key]->nodes[$node][0]);

                $spelled_words[ $words[$word_key] ]["all"][$path_id]["elements"][] = array( //
                        "symbol" => ucfirst($graphList[$word_key]->nodes[$node][0]),
                        "number" => array_searchi(ucfirst($graphList[$word_key]->nodes[$node][0]), $symbolsList),
                        "url" => "img.php?e=".ucfirst($graphList[$word_key]->nodes[$node][0])."&w=128"
                );
            }

            if (count($paths) < $shortest_length) {
                $shortest_length = count($paths);
                $spelled_words[ $words[$word_key] ]["shortest"][0] = $spelled_words[ $words[$word_key] ]["all"][$path_id];
            }

//print("<br>\n".implode("-", $spelled_word[$word_key])."<br>\n");
//print_r($spelled_word[$word_key]);
        }
    }
//print_r($spelled_words);
    $spelled_words_json = json_encode($spelled_words);

    header('Content-Type: application/json');
    print($spelled_words_json);
}
?>
