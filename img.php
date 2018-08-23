<?PHP
require 'functions.php';

$element = $_GET["e"];
$elementWidth = $_GET["w"];
if ( ($elementWidth == 0) || ($elementWidth == "") ) $elementWidth = 512;

$db = connectDB();
$symbolList = getSymbolsFromDB($db);

$symbolId = array_searchi($element, $symbolList);

if ($symbolId) {
    $elementList = getElementsFromDB($db);
    $info = array(
        "symbol" => $elementList[$symbolId]["symbol"],
        "name" => $elementList[$symbolId]["name"],
        "number" => substr( "00" . $elementList[$symbolId]["number"], -3)
    );
} else {
    $info = array(
        "symbol" => "XX",
        "name" => "Unknown",
        "number" => 0
    );
}

$path = __DIR__ . DIRECTORY_SEPARATOR . "p/512/";
$filename = $info["number"] . "atomo" . strtolower($info["symbol"]) . "512.png";
//print ($path.$filename);

$img = @imagecreatefrompng($path.$filename);

if (!$img) {
    $img = createLocalImage($info);
}

$imgscaled = imagescale($img, $elementWidth, $elementWidth, IMG_BICUBIC);

header('Content-type:image/png');
imagepng($imgscaled);

imagedestroy($img);
imagedestroy($imgscaled);

function createLocalImage($info) {
    print_r($info);
    $BOX_WIDTH = 80;
    $BOX_HEIGHT = 80;
    $SYMBOL_SIZE = 25;
    $NAME_SIZE = 8;
    $NUM_SIZE = 10;

    $sheight = 50;
    $numheight = 15;
    $nameheight = 15;
    $sheight = $sheight + 5;
    $sheight = $sheight + 5;
    $numheight = 30;


    $img = @imagecreatetruecolor($BOX_WIDTH, $BOX_HEIGHT);

    $white = imagecolorallocate($img, 230, 230, 230);
    $black = imagecolorallocate($img, 25, 25, 25);

    $font = __DIR__ . DIRECTORY_SEPARATOR . "Lato-Regular.ttf";
    $symbolfont = __DIR__ . DIRECTORY_SEPARATOR . "Lato-Bold.ttf";

    // outer borders
    imageline($img, 0, 0, $BOX_WIDTH, 0, $black); // top
    imageline($img, 0, $BOX_HEIGHT - 1, $BOX_WIDTH, $BOX_HEIGHT - 1, $black); // bottom
    imageline($img, 0 , 0, 0 , $BOX_HEIGHT, $black); // left
    imageline($img, $BOX_WIDTH - 1 , 0, $BOX_WIDTH - 1 , $BOX_HEIGHT, $black); // right

    // symbol
    $box = imagettfbbox($SYMBOL_SIZE, 0, $symbolfont, $info["symbol"]);
    imagettftext($img, $SYMBOL_SIZE, 0, ( $BOX_WIDTH / 2) - ( ($box[2] - $box[0]) / 2 ), $sheight, $black, $symbolfont, $info["symbol"]);

    // name
    $box = imagettfbbox($NAME_SIZE, 0, $font, $info["name"]);
    imagettftext($img, $NAME_SIZE, 0, ( $BOX_WIDTH / 2) - ( ($box[2] - $box[0]) / 2 ), $nameheight, $black, $font, $info["name"]);

    // number
    $box = imagettfbbox($NUM_SIZE, 0, $symbolfont, $info["number"]);
    imagettftext($img, $NUM_SIZE, 0, ( $BOX_WIDTH / 2) - ( ($box[2] - $box[0]) / 2 ), $numheight, $black, $symbolfont, $info["number"]);

    return $img;
}
?>
