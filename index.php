<!DOCTYPE html>
<html>
    <head>
        <title>At O Mo speller</title>
        <style type="text/css">
            .headerWrapper {
                text-align: center;
                margin: 2em 0;
            }
            .header {
                    display: inline-block;
                    font-size: 2.0em;
            }
            .superscript {
                color: red;
                font-size: .42em;
                vertical-align: super;
            }
            .formWrapper {
                text-align: center;
            }
            .form {
                display: inline-block;
            }
            #word {
                font-size: 1.6em;
            }
            #lookup {
                font-size: 1.6em;
            }
            footer {
                top: 1em;
                right: 1em;
                background-color: #eeeeee;
                color: #444444;
                position: absolute;
                padding: 0.5em;
                width: 300px;
            }
            .disclaimer {
                position: relative;
            }
        </style>
    </head>
    <body onload="">
        <div class="headerWrapper">
            <div class="header">
                <img src="img.php?e=At&amp;w=96">
                <img src="img.php?e=O&amp;w=96">
                <img src="img.php?e=Mo&amp;w=96">
                &nbsp;Speller <span class="superscript">(alpha)</span>
            </div>
        </div>
        <div class="formWrapper">
            <div class="form">
                <input type="text" id="word" name="word">
                <button onclick="atomoSpell2()" id="lookup">Look up</button>
            </div>
            <div class="form_options">
                <input type="radio" id="length_all" name="length" value="0" checked><label for="length_all">All</label>
                <input type="radio" id="length_shortest" name="length" value="1"><label for="length_shortest">Shortest</label>
            </div>
        </div>
        <div id="spelled">

        </div>
        <footer>
            <div class="disclaimer">This release of our AtOMo Speller is an Alpha release. Some planned features are not yet implemented. We appreciate your feedback on the accuracy of the spelling results, and possible improvements. Connect to our channels:
                <ul>
                    <li><a href="https://t.me/At_O_Mo">Telegram</a></li>
                    <li><a href="https://twitter.com/Blockchainizatr/">Twitter</a></li>
                </ul>
            </div>
        </footer>
    </body>
    <script type="text/javascript" src="atomo.js"></script>
    <script type="text/javascript">
        document.getElementById("word").addEventListener("keyup", function(event) {
            event.preventDefault();
            if (event.keyCode === 13) {
                document.getElementById("lookup").click();
            }
        });
    </script>
</html>
