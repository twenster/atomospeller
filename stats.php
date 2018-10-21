<?php
require("functions.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>At O Mo speller</title>
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
        <link rel="stylesheet" href="res/style.css">
    </head>
    <body onload="">
        <div class="atomo-page-wrapper">
            <div class="pure-g atomo-header-wrapper">
                <div class="pure-u-1-1 atomo-disclaimer">This is an Alpha release. Some planned features are being implemented. Give us feedback: <a href="https://t.me/At_O_Mo">Telegram</a>, <a href="https://twitter.com/Blockchainizatr/">Twitter</a></div>
                <div class="pure-u-1-1 atomo-header">
                    <img src="img/atomo-logo.png" title="AtOMo" alt="AtOMo logo">
                    &nbsp;Speller</span>
                </div>
            </div>
            <div class="pure-g atomo-form-wrapper">
                <div class="pure-u-1-1"><h1>Search Statistics</h1></div>
                <div class="pure-u-1-2">
<?php
$statList = getStatsbyWordCount();
$html = "<div class=\"atomo-table-select\"><table class=\"pure-table\"><thead><tr>".
        "<th>Sorted by count</th><th>word</th>".
        "</tr></thead>".
        "<tbody>";
foreach($statList as $id => $stat) {
    $html = $html.
        "<tr><td>".$stat["word_count"]."</td><td>".$stat["word"]."</td></tr>";
}
$html = $html.
    "</table></div>";
print($html);
?>
                </div>
                <div class="pure-u-1-2">
<?php
$statList = getStatsbyDate();
$html = "<div class=\"atomo-table-select\"><table class=\"pure-table\"><thead><tr>".
        "<th>Sorted by Date</th><th>Word</th><th>Country</th>".
        "</tr></thead>".
        "<tbody>";
foreach($statList as $id => $stat) {
    $html = $html.
        "<tr><td>".date("d M Y", strtotime($stat["word_date"]))."</td><td>".$stat["word"]."</td><td>".$stat["country"]."</td></tr>";
}
$html = $html.
    "</table></div>";
print($html);
?>
                </div>
            </div>
        </div>
        <footer>
        </footer>
    </body>
    <script type="text/javascript" src="res/atomo.js"></script>
</html>
