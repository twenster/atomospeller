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
                <div class="pure-u-1-1 pure-form atomo-form">
                    <input type="text" id="word" name="word" class="atomo-word">
                    <button onclick="atomoSpell()" id="lookup" class="pure-button pure-button-primary atomo-lookup">Look up</button>
                </div>
            </div>
            <div id="atomo-search-result" class="pure-g atomo-results-wrapper">
            </div>

            <div id="atomo-spelled-image" class="pure-g atomo-results-wrapper">
            </div>

            <div id="spelled">
            </div>
        </div>
        <footer>
        </footer>
    </body>
    <script type="text/javascript" src="res/atomo.js"></script>
</html>
