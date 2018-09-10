document.getElementById("word").addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("lookup").click();
    }
});

function atomoSpell2() {
    word = document.getElementById("word").value;
    length = document.getElementsByName("length").value;
    responseHTML = "";
    httpGetAsync("api2.php?q="+word+"&l="+length, function(response) {
        var thisResponse = JSON.parse(response);

        queryLength = (document.querySelector('input[name="length"]:checked').value==1) ? "shortest" : "all";
        console.log("length="+queryLength);
        for (var queryWordId in thisResponse) {
            //responseHTML = responseHTML + "<div class=\"pure-u-1-1 atomo-results\">Query:";

            for (spellingsId in thisResponse[queryWordId][queryLength]) {

                elementalWord = thisResponse[queryWordId][queryLength][spellingsId]["elemental_word"];
                elementsHTML = "";
                for (var element in thisResponse[queryWordId][queryLength][spellingsId]["elements"]) {
                    elementsHTML = elementsHTML + "<img src=" + thisResponse[queryWordId][queryLength][spellingsId]["elements"][element]["url"] + ">";
                }

                //responseHTML = responseHTML + "&nbsp;<b>Suggestion :</b> " + elementalWord + "<br>\n";
                responseHTML = responseHTML + "<div class=\"pure-u-1-1 atomo-results\">" + elementsHTML + "</div>\n";

            }

            responseHTML = responseHTML + "</div>\n";

        }

        // display the result
        document.getElementById("spelled").innerHTML = responseHTML;
        //console.log(responseHTML);
    });
}

function httpGetAsync(theUrl, callback) {
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function() {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      callback(xmlHttp.responseText);
    }
  }
  xmlHttp.open("GET", theUrl, true); // true for asynchronous
  xmlHttp.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
  xmlHttp.send(null);
}
