<!DOCTYPE html>
<html>
    <head>
        <title>At O Mo speller</title>
        <script type="text/javascript" src="atomo.js"></script>
    </head>
    <body onload="">
        <div if="form">
            <input type="text" id="word">
            <input type="hidden" id="len" value="2">
            <button onclick="atomoSpell()" id="button">Look up</button>
        </div>
        <div id="spelled">
            
        </div>
    </body>
</html>