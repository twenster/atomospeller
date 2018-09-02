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
                <input type="text" id="word" name="word">
                <button onclick="atomoSpell2()" id="lookup">Look up</button>
            </div>
        </div>
        <div id="spelled">

        </div>
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
