function atomoSpell() {
    word = document.getElementById("word").value;
    //len = document.getElementById("len").value;
    responseHTML = "";
    httpGetAsync("api.php?q="+word, function(response) {
        var thisResponse = JSON.parse(response);

        for (var wordId in thisResponse.wordlist) {
            queryWord = thisResponse.wordlist[wordId].query;
            responseHTML = responseHTML + "<div class=\"suggestion\"><h2>Queried word: " + queryWord + "</h2><br>\n";

            for (suggestionsId in thisResponse.wordlist[wordId].suggestions) {

                thisSuggestion = thisResponse.wordlist[wordId].suggestions[suggestionsId].suggestion;
                elementsHTML = "";
                for (var el in thisResponse.wordlist[wordId].suggestions[suggestionsId].elements) {
                    elementsHTML = elementsHTML + " <img src=" + thisResponse.wordlist[wordId].suggestions[suggestionsId].elements[el].url + ">";
                }

            responseHTML = responseHTML + "&nbsp;<b>Suggestion " + suggestionsId + ":</b> " + thisSuggestion + "<br>\n";
            responseHTML = responseHTML + "&nbsp;" + elementsHTML + "<br>\n";

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
