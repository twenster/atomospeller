document.getElementById("word").addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("lookup").click();
    }
});

function atomoSpell2() {
    word = document.getElementById("word").value;
    length = document.getElementsByName("length").value;
    imgWidth = 128;
    responseHTML = "";
    httpGetAsync("api2.php?q="+word+"&l="+length, function(response) {
        var thisResponse = JSON.parse(response);
        var queryLength = (document.querySelector('input[name="length"]:checked').value==1) ? "shortest" : "all";

        for (var queryWordId in thisResponse) {
            for (spellingsId in thisResponse[queryWordId][queryLength]) {
                symbolList = thisResponse[queryWordId][queryLength][spellingsId]["symbols"];
                elemental_word = thisResponse[queryWordId][queryLength][spellingsId]["elemental_word"];
                responseHTML = responseHTML
                             + "<div class=\"pure-u-1-1 atomo-results\">"
                             + "<img src='img.php?s=" + JSON.stringify(symbolList) + "&w=" + imgWidth + "' alt='"+elemental_word+"'>"
                             + "</div>\n";
            }
            responseHTML = responseHTML + "</div>\n";
        }

        // display the result
        document.getElementById("spelled").innerHTML = responseHTML;
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
