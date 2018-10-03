<?PHP
function composeImage($requestedElementList, $requestedElementWidth) {
    // Create the final image canvas.
    // We will copy/merge image to this new image and build a word
    $wordImage = imagecreatetruecolor(count($requestedElementList) * SOURCEELEMENTWIDTH, SOURCEELEMENTWIDTH);

    // Transparent background (PNG)
    imagesavealpha($wordImage, true);
    $trans_colour = imagecolorallocatealpha($wordImage, 0, 0, 0, 127);
    imagefill($wordImage, 0, 0, $trans_colour);

    // Compose the new image,
    // Adds each element from the array to the image canvas
    $dbElementList = getElementsFromDB();
    $dbSymbolList = getSymbolsFromDB();
    foreach ($requestedElementList as $elementPosition => $requestedElement) {
        $symbolId = array_searchi($requestedElement, $dbSymbolList);

        // if the requestedElement exists in the Element from the database,
        // We load the image file, or we create an empty one
        if ( $symbolId ) {
            $sourceElementFilename = substr( "00" . $dbElementList[$symbolId]["number"], -3) . "atomo" . strtolower($dbElementList[$symbolId]["symbol"]) . "512.png";
            $requestedElementImage = @imagecreatefrompng(SOURCEELEMENTPATH.$sourceElementFilename);
        } else {
            $requestedElementImage = createLocalImage( array(
                "symbol" => "XX",
                "name" => "Unknown",
                "number" => 0
                )
            );
        }

        // copy the element image to our own image.
        // in this case imagecopymerge() = imagecopy() because of the last parameter = 100
        $r = imagecopymerge($wordImage, $requestedElementImage, $elementPosition * SOURCEELEMENTWIDTH, 0, 0, 0, SOURCEELEMENTWIDTH, SOURCEELEMENTWIDTH, 100);
    }

    // Resize the image to the size given in the URL
    $wordImageScaled = imagescale($wordImage, count($requestedElementList) * $requestedElementWidth, $requestedElementWidth, IMG_BICUBIC);

    header('Content-type:image/png');
    header('Content-Disposition: inline; filename="AtOMo-' . implode($requestedElementList) . '.png"');
    imagepng($wordImageScaled);
    //imagepng($wordImage);

    imagedestroy($wordImage);
    imagedestroy($wordImageScaled);

}

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

    $font = __DIR__ . DIRECTORY_SEPARATOR . "res/Lato-Regular.ttf";
    $symbolfont = __DIR__ . DIRECTORY_SEPARATOR . "res/Lato-Bold.ttf";

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
