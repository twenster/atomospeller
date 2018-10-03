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
                <div class="pure-u-1-1 atomo-header">
                    <img src="img/atomo-logo.png" title="AtOMo" alt="AtOMo logo">
                    &nbsp;Speller</span>
                </div>
            </div>
            <div class="pure-g atomo-form-wrapper">
                <div class="pure-u-1-1 atomo-form">
                    <input type="text" id="word" name="word" class="atomo-word">
                    <button onclick="atomoSpell()" id="lookup" class="atomo-lookup">Look up</button>
                </div>
                <div class="pure-u-1-1 pure-menu pure-menu-horizontal atomo-form-options">
                    <ul class="pure-menu-list">
                        <li class="pure-menu-item"><input type="radio" id="length_all" name="length" value="0" checked><label for="length_all">All</label></li>
                        <li class="pure-menu-item"><input type="radio" id="length_shortest" name="length" value="1"><label for="length_shortest">Shortest</label></li>
                </div>
            </div>
            <div id="spelled" class="pure-g atomo-results-wrapper">
            </div>
        </div>
        <footer>
            <div class="atomo-disclaimer">This release of our AtOMo Speller is an Alpha release. Some planned features are being implemented. We appreciate your feedback on the accuracy of the spelling results, and possible improvements. Connect to our channels:
                <ul>
                    <li><a href="https://t.me/At_O_Mo">Telegram</a></li>
                    <li><a href="https://twitter.com/Blockchainizatr/">Twitter</a></li>
                </ul>
            </div>
        </footer>
    </body>
    <script type="text/javascript" src="res/atomo.js"></script>
</html>
