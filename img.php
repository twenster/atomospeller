<?PHP
require 'functions.php';
require 'foundry.php';

// Arguments
// img.php?s=Ni-Ce,Na-Ce&w=256&d=inline
$symbolList = $_GET["s"];
$elementWidth = $_GET["w"]; // = 256
$wordDisplay = $_GET["d"]; // = inline, left

$wordImage = composeImage($symbolList, $elementWidth, $wordDisplay);
displayImage($symbolList, $wordImage);
?>
