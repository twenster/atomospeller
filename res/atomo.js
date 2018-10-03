document.getElementById("word").addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("lookup").click();
    }
});

function atomoSpell() {
    word = document.getElementById("word").value;
    length = document.getElementsByName("length").value;
    imgWidth = 128;
    responseHTML = "";
    httpGetAsync("api.php?t=search&q="+word+"&l="+length, function(response) {
//    httpPostAsync("api.php", "q="+word+"&l="+length, function(response) {

        console.log(response);
        var thisResponse = JSON.parse(response);
        var queryLength = (document.querySelector('input[name="length"]:checked').value==1) ? "shortest" : "all";

        for (var queryWordId in thisResponse) {
            for (spellingsId in thisResponse[queryWordId][queryLength]) {
                symbolList = thisResponse[queryWordId][queryLength][spellingsId]["symbols"];
                symbolized_word = thisResponse[queryWordId][queryLength][spellingsId]["symbolized_word"];
                slackemoji = (':atomo' + symbolList.join(': :atomo') + ':').toLowerCase();
                responseHTML = responseHTML
                             + "<div class=\"pure-u-1-1 atomo-result\">"
                             + "<div class=\"pure-u-1-1 atomo-symbolized\">"
                             + "Symbolized: "+symbolized_word
                             + "</div>"
                             + "<div class=\"pure-u-1-1 atomo-slack\">"
                             + "Slack emoji: "+slackemoji
                             + "</div>"
                             + "<div class=\"pure-u-1-1 atomo-image\">"
                             //+ "<img src='img.php?s=" + JSON.stringify(symbolList) + "&w=" + imgWidth + "' alt='"+symbolized_word+"'>"
                             + "<img src='api.php?t=image&s=" + JSON.stringify(symbolList) + "&w=" + imgWidth + "' alt='"+symbolized_word+"'>"
                             + "</div>"
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
        if (xmlHttp.readyState == XMLHttpRequest.DONE && xmlHttp.status == 200) { // XMLHttpRequest.DONE = 4
            callback(xmlHttp.responseText);
        }
    }

    xmlHttp.open("GET", theUrl, true); // true for asynchronous
    xmlHttp.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    xmlHttp.send(null);
}

function httpPostAsync(theUrl, theParameters, callback) {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == XMLHttpRequest.DONE && xmlHttp.status == 200) { // XMLHttpRequest.DONE = 4
            callback(xmlHttp.responseText);
        }
    }

    xmlHttp.open("POST", theUrl, true); // true for asynchronous
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    xmlHttp.send(theParameters);
}
