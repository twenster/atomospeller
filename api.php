<?php
require("functions.php");

$foundElementArray = array();
$elementedWordArray = array();
$jsonElementArray = array();
$wordQuery = $_GET["q"]; // query
$lengthPref = 2;
$lengthOrder = array(2, 1);

// affiche la page

// envoie ue requête jquery pour récupérer le contenu
// ou bien se recharge en envoyant les données
if ($wordQuery != null) {
    $db = connectDB();

    // recupère le liste des elements
    $elementsList = getElementsFromDB($db);
    $symbolsList = getSymbolsFromDB($db);

    //echo "<h1 style='margin-top: 1em'>";

    // crée une table avec chaque mot entrée
    $words = preg_replace('/[\W]+/', '', explode(' ', $wordQuery));

    foreach ($words as $i => $word) {
        foreach($lengthOrder as $lengthPref) {

            //$found = searchElementFromHead($word, $elementList, $lengthPref, $foundElementArray);
            //$elementedWordArrayByLength[$lengthPref][$word] = $foundElementArray;
            $foundElementArray = array();
            $endOfWord = searchElement($word, $lengthPref);
            $elementedWordArrayByWord[$word]["all"][$lengthPref] = $foundElementArray;

        }

        // which foundElement is the shortest for current word?
        if (count($elementedWordArrayByWord[$word]["all"][2]) <= count($elementedWordArrayByWord[$word]["all"][1]))
            $elementedWordArrayByWord[$word]["shortest"] = $elementedWordArrayByWord[$word]["all"][2];
        else
            $elementedWordArrayByWord[$word]["shortest"] = $elementedWordArrayByWord[$word]["all"][1];
    }

    foreach ($elementedWordArrayByWord as $thisWord => $thisWordArray) {

        $thisEntry = array(
            "query" => $thisWord,
            "suggestionList" => array()
        );

        //foreach ($thisWordArray as $thisLength => $thisElementArray) {
        $thisElementArray = $thisWordArray["shortest"];

            $thisImageArray = array();
            foreach ($thisElementArray as $thisImage) {
                $thisImageArray[] = array(
                    "symbol" => $thisImage,
                    "number" => array_searchi($thisImage, $symbolsList),
                    "url" => "img.php?e=".$thisImage."&w=128"
                );
            }

            $thisEntry["suggestionList"][] = array(
                "suggestion" => implode($thisElementArray),
                "elementList" => $thisImageArray
            );
        //}

        array_push($elementedWordArray, $thisEntry);
    }
    $jsonElementArray = $elementedWordArray;

    $asset = array(
        "query" => $words,
        "wordlist" => $jsonElementArray,

        "source" => array(
            "email" => "",
            "website" => array(
                array(
                    "name" => "ATO main",
                    "url" => "https://app.bookoforbs.com/ato/"
                ),
                array(
                    "name" => "ATO market",
                    "url" => "https://app.bookoforbs.com/ato/"
                ),
            ),
            "social" => array(
                array(
                    "name" => "facebook",
                    "url" => "https://www.facebook.com/blockchainizator/"
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
