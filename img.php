<?PHP
require 'functions.php';

$element = $_GET["e"];

$db = connectDB();
$symbolList = getSymbolsFromDB($db);

$symbolId = array_searchi($element, $symbolList);

if ($symbolId) {
    $elementList = getElementsFromDB($db);
    $info = array(
        "symbol" => $elementList[$symbolId]["symbol"],
        "name" => $elementList[$symbolId]["name"],
        "number" => $elementList[$symbolId]["number"]
    );
} else {
    $info = array(
        "symbol" => "XX",
        "name" => "Unknown",
        "number" => 0
    );

}

$BOX_WIDTH = 80;
$BOX_HEIGHT = 80;
$SYMBOL_SIZE = 25;
$NAME_SIZE = 8;
$NUM_SIZE = 10;

$sheight = 50;
$numheight = 15;
$nameheight = 15;
//if($shownum || $showname)
    $sheight = $sheight + 5;
//if($showname && $shownum) {
    $sheight = $sheight + 5;
    $numheight = 30;
//}


$img = ImageCreate($BOX_WIDTH, $BOX_HEIGHT);

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

Header('Content-type:image/png');
ImagePNG($img);
ImageDestroy($img);
?>