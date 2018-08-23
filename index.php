<!DOCTYPE html>
<html>
    <head>
        <title>At O Mo speller</title>
        <script type="text/javascript" src="atomo.js"></script>
        <style type="text/css">
            .headerWrapper {
                text-align: center;
                margin: 2em 0;
            }
            .header {
                    display: inline-block;
                    font-size: 2.0em;
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
        </style>
    </head>
    <body onload="">
        <div class="headerWrapper">
            <div class="header">
                <img src="img.php?e=At&amp;w=96">
                <img src="img.php?e=O&amp;w=96">
                <img src="img.php?e=Mo&amp;w=96">
                &nbsp;Speller
            </div>
        </div>
        <div class="formWrapper">
            <div class="form">
                <input type="text" id="word">
                <input type="hidden" id="len" value="2">
                <button onclick="atomoSpell()" id="lookup">Look up</button>
            </div>
        </div>
        <div id="spelled">

        </div>
    </body>
</html>
