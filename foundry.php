<?PHP
function composeImage($symbolList, $elementWidth, $wordDisplay) {
    $numberOfElement = 0;
    $wordSpacing = 20;
    $elementStartPositionX = 0;
    $elementStartPositionY = 0;
    $dbElementList = getElementsFromDB();
    $dbSymbolList = getSymbolsFromDB();
    $wordList = explode(",", $symbolList);
    $longestWordLength = 0;

    if ( ($elementWidth == 0) || ($elementWidth == "") || ($elementWidth == "undefined"))
        $elementWidth = SOURCEELEMENTWIDTH;

    /*
     * Transform URI argumets Ni-Ce,Na-Ce into arrays
     * $wordElement[0] = [Ni, Ce]
     * $wordElement[1] = [Na, Ce]
     */
    foreach ($wordList as $wordPosition => $thisWord) {
        $wordElement [$wordPosition] = explode("-",$thisWord);
        $numberOfElement += count($wordElement [$wordPosition]);
        $longestWordLength = ($longestWordLength < count($wordElement [$wordPosition])) ? count($wordElement [$wordPosition]) : $longestWordLength;
    }


    /*
     * Create the final image canvas.
     * We will copy/merge image to this new image and build a word
     */
    if ($wordDisplay == "left") {
        $wordCanvas = imagecreatetruecolor( $longestWordLength * SOURCEELEMENTWIDTH, count($wordList) * SOURCEELEMENTWIDTH + (count($wordList)-1) * $wordSpacing);
    } else { // inline
        $wordCanvas = imagecreatetruecolor( ($numberOfElement * SOURCEELEMENTWIDTH) + ( (count($wordList) - 1) * $wordSpacing ), SOURCEELEMENTWIDTH);
    }

    // Transparent background (PNG)
    imagesavealpha($wordCanvas, true);
    $trans_colour = imagecolorallocatealpha($wordCanvas, 0, 0, 0, 127);
    imagefill($wordCanvas, 0, 0, $trans_colour);

    /*
     * Compose the new image,
     * Adds each element from the array to the image canvas
     */
    foreach ($wordList as $wordPosition => $thisWord) {
        foreach ($wordElement [$wordPosition] as $elementPosition => $requestedElement) {
            $symbolId = array_searchi($requestedElement, $dbSymbolList);

            // if the requestedElement exists in the Element from the database,
            // We load the image file, or we create an empty one
            if ( $symbolId ) {
                $sourceElementFilename = substr( "00" . $dbElementList[$symbolId]["number"], -3) . "atomo" . strtolower($dbElementList[$symbolId]["symbol"]) . "256.png";
                $requestedElementImage = @imagecreatefrompng(SOURCEELEMENTPATH.$sourceElementFilename);
            } else {
                $requestedElementImage = createLocalImage( array(
                    "symbol" => "XX",
                    "name" => "Unknown",
                    "number" => 0
                    )
                );
            }

            /*
             * Copy the element image to our own image.
             * in this case imagecopymerge() = imagecopy() because of the last parameter = 100
             */
            //$r = imagecopymerge($wordCanvas, $requestedElementImage, $elementPosition * SOURCEELEMENTWIDTH, 0, 0, 0, SOURCEELEMENTWIDTH, SOURCEELEMENTWIDTH, 100);
            $r = imagecopymerge($wordCanvas, $requestedElementImage, $elementStartPositionX + ($elementPosition * SOURCEELEMENTWIDTH), $elementStartPositionY, 0, 0, SOURCEELEMENTWIDTH, SOURCEELEMENTWIDTH, 100);
        }
        // Saving the size of the current word for the x position of the next word

        if ($wordDisplay == "left") {// words are left aligned
            $elementStartPositionX = 0;
            $elementStartPositionY += $wordSpacing + SOURCEELEMENTWIDTH;
        } else { // default = inline
            $elementStartPositionX += $wordSpacing + count($wordElement [$wordPosition]) * SOURCEELEMENTWIDTH;
            $elementStartPositionY = 0;
        }
    }

    /*header('Content-type:image/png');
    header('Content-Disposition: inline; filename="AtOMo-' . implode($wordList) . '.png"');
    imagepng($wordCanvas);

    imagedestroy($wordCanvas);
    */
    return $wordCanvas;
}

/*
 * send image back to the browser
 */
function displayImage($symbolList, $wordCanvas) {
    $wordList = explode(",", $symbolList);

    header('Content-type:image/png');
    header('Content-Disposition: inline; filename="AtOMo-' . implode($wordList) . '.png"');
    imagepng($wordCanvas);

    imagedestroy($wordCanvas);
}

/*
 * Creates a default image element
 */
function createLocalImage($info) {
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
