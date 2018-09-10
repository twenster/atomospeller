<html>
<head>
    <title>Installation</title>
</head>
<body>
<?php
// Install.php

/* DB format
    table 'periodic' : id, symbol, name, number, mass
    table 'spelled'  : phrase, count
    table 'metaphone': words, metaphone
*/
// Variables
require("functions.php");

// DataBase connexion
$db = connectDB();

/* Source:
    Periodic-Table-JSON : https://github.com/Bowserinator/Periodic-Table-JSON
        CC-BY-SA 4.0 International
*/
$str = file_get_contents('PeriodicTableJSON.json');
$json = json_decode($str, true);

foreach ($json['elements'] as $el => $data) {
    print("element:".$el." => ".$data["name"].", ".$data["number"].". ".$data["symbol"]."<br>");

    $db->prepare("INSERT INTO `elements` (name, number, symbol) VALUES ('".$data["name"]."', ".$data["number"].", '".$data["symbol"]."')")
       ->execute();
}
?>
installed
</body>
</html>