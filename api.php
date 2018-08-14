<?php
require("functions.php");

$foundElementArray = array();
$elementedWordArray = array();
$jsonElementArray = array();
$wordQuery = $_GET["q"]; // query
//$lengthPref = $_GET['len']; // 1 ou 2
$lengthOrder = array(2, 1);

// affiche la page

// envoie ue requête jquery pour récupérer le contenu
// ou bien se recharge en envoyant les données
if ($wordQuery != null) {
    $db = connectDB();

    // recupère le liste des elements
    $elementList = getSymbolsFromDB($db);

    $solvable = true;
    //echo "<h1 style='margin-top: 1em'>";

    // crée une table avec chaque mot entrée
    $words = preg_replace('/[\W]+/', '', explode(' ', $wordQuery));

    foreach($lengthOrder as $lengthPref) {
        foreach ($words as $i => $word) {
//print("word:".$word."<br>\n");
            $found = searchElementFromHead($word, $elementList, $lengthPref, $foundElementArray);
            $elementedWordArrayByLength[$lengthPref][$word] = $foundElementArray;
            $elementedWordArrayByWord[$word][$lengthPref] = $foundElementArray;
            $foundElementArray = array();
//print_r($foundElementArray);
        }
    }

//    print_r($elementedWordArrayByLength);
//    print_r($elementedWordArrayByWord);
//    print("<br>\n");

    foreach ($elementedWordArrayByWord as $thisWord => $thisWordArray) {
//print("Word: ".$thisWord."<br>\n");
        $thisEntry = array(
            "query" => $thisWord,
            "suggestions" => array()
        );
        foreach ($thisWordArray as $thisLength => $thisElementArray) {

            $thisImageArray = array();
            foreach ($thisElementArray as $thisImage) {
                $thisImageArray[] = array(
                    "symbol" => $thisImage,
                    "url" => "img.php?e=".$thisImage
                );
            }

            $thisEntry["suggestions"][] = array(
                "suggestion" => implode($thisElementArray),
                "preferedlength" => $thisLength,
                "elements" => $thisImageArray
            );
        }
        array_push($elementedWordArray, $thisEntry);
    }
    $jsonElementArray = $elementedWordArray;

//print_r($elementedWordArray);

    $asset = array(
        "query" => $words,
        "wordlist" => $jsonElementArray,

        "source" => array(
            "email" => "",
            "website" => array(
                array(
                    "name" => "ATO main",
                    "url" => "https://app.bookoforbs.com/ato"
                ),
                array(
                    "name" => "ATO market",
                    "url" => "https://app.bookoforbs.com/ato/LATOMONA/market/"
                ),
            ),
            "social" => array(
                array(
                    "name" => "facebook",
                    "url" => "https://www.facebook.com/blockchainizator"
                ),
                array(
                    "name" => "twitter",
                    "url" => "https://twitter.com/Blockchainizatr/"
                ),
                array(
                    "name" => "instagram",
                    "url" => "https://www.instagram.com/blockchainizator/"
                )
            )
        )
    );
//print_r($asset);
    $assetJson = json_encode($asset);

    header('Content-Type: application/json');
    print($assetJson);
}
?>
