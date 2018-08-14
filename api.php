<?php
require("functions.php");

$foundElementArray = array();
$jsonElementArray = array();
$wordQuery = $_GET["word"]; // query
$lengthPref = $_GET['len']; // 1 ou 2

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

    foreach ($words as $word) {
//print("word:".$word."<br>\n");
        searchElementFromHead($word, $elementList, $lengthPref, $foundElementArray);
//print_r($foundElementArray);
    }

//    print_r($foundElementArray);
//    print("<br>\n");

    foreach ($foundElementArray as $element) {
//print("Element: ".$element."<br>\n");
        $thisElement = array(
            "title" => $element,
            "image" => array(
                "title" => $element,
                "url" => "img.php?e=".$element
            )
        );
        array_push($jsonElementArray, $thisElement);
    }

    $asset = array(
        "asset" => $words,
        "component" => $jsonElementArray,
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

    $assetJson = json_encode($asset);

    header('Content-Type: application/json');
    print($assetJson);
} 
?>
