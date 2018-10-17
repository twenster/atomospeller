document.getElementById("word").addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("lookup").click();
    }
});

// Globals
var atomoSearchResultJSON = "";
var atomoSelectedWords = [];

function atomoSpell() {
    word = document.getElementById("word").value;
    length = document.getElementsByName("length").value;

    document.getElementById("atomo-search-result").innerHTML = "";
    document.getElementById("atomo-spelled-image").innerHTML = "";

    httpGetAsync("api.php?t=search&q="+word+"&l="+length, atomoSpellHub);
//    httpPostAsync("api.php", "q="+word+"&l="+length, atomoSpellHub());
}

function atomoSpellHub(response) {
    console.log(response);
    atomoSearchResultJSON = JSON.parse(response);
    var queryLength = "all";
    var imgWidth = atomoSearchResultJSON["globals"]["source_element_width"];
    var searchResultHTML = "";

    // prepare global variable
    for (var queryWordId in atomoSearchResultJSON) {
        atomoSelectedWords[queryWordId] = "";
    }

    // table header
    searchResultHTML = "<div class=\"pure-u-1-1\">"
        + "<p>Each word may have different spelling, select your prefered word to compose you sentence</p>"
        + "<div class=\"atomo-table-select\"><table class=\"pure-table\"><thead><tr>";
    for (var queryWordId in atomoSearchResultJSON) {
        if (queryWordId != "globals") { // globals contains some server variable
            searchResultHTML = searchResultHTML
                + "<th>" + queryWordId + "</th>";
        }
    }
    searchResultHTML = searchResultHTML
        + "</tr></thead>";

    // table row
    searchResultHTML = searchResultHTML
        + "<tbody><tr>";
    for (var queryWordId in atomoSearchResultJSON) {
        if (queryWordId != "globals") { // globals contains some server variable
            searchResultHTML = searchResultHTML
                + "<td><select id=\"atomo-" + queryWordId + "\" class=\"atomo-select\" size=\"5\" onChange=\"atomoSaveWord('"+queryWordId+"')\">";
            for (spellingsId   in atomoSearchResultJSON [ queryWordId ][ queryLength] ) {
                symbol_list     = atomoSearchResultJSON [ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].join("-");
                symbolized_word = atomoSearchResultJSON [ queryWordId ][ queryLength ][ spellingsId ][ "symbolized_word" ];

                searchResultHTML = searchResultHTML
                    + "<option class=\"pure-button\" value=\"" + symbolized_word + "\">" + symbol_list + "</option>";
            }
            searchResultHTML = searchResultHTML
                + "</select></td>";
        }
    }
    searchResultHTML = searchResultHTML
        + "</tr></table></div>"
        + "<div class=\"atomo-table-button\"><button class=\"pure-button atomo-lookup\" onClick=\"atomoComposeImage()\">Create image</button></div></div>";


    // display the result
    document.getElementById("atomo-search-result").innerHTML = searchResultHTML;
}

function atomoComposeImage() {
    var queryLength = "all";
    var imgWidth = 256;
    var symbolized_word_list = "";
    var slackemoji_list = "";
    var symbols_list = [];
    var spelledImageHTML = "";

//    for (var queryWordId in atomoSelectedWords) {
    for (var queryWordId in atomoSearchResultJSON) {
        if (queryWordId != "globals") {

            symbolized_word_list = symbolized_word_list + atomoSearchResultJSON[ queryWordId ][ "selected" ] + " ";

            for (var all_symbolized in atomoSearchResultJSON[ queryWordId ][ queryLength ]) {
                if (atomoSearchResultJSON[ queryWordId ][ queryLength ] [ all_symbolized ][ "symbolized_word" ] == atomoSearchResultJSON[ queryWordId ][ "selected" ]) {
                    slackemoji_list = slackemoji_list + " " + (':atomo' + atomoSearchResultJSON[ queryWordId ][ queryLength ] [ all_symbolized ][ "symbols" ].join(': :atomo') + ':').toLowerCase();
                    symbols_list = symbols_list.concat(atomoSearchResultJSON[ queryWordId ][ queryLength ] [ all_symbolized ][ "symbols" ]);
                }
            }
        }
    }

    spelledImageHTML = spelledImageHTML
        + "<div class=\"pure-u-1-1 atomo-result\">"
        + "<div class=\"pure-u-1-1 atomo-symbolized\">"
        + "Symbolized: " + symbolized_word_list
        + "</div>"
        + "<div class=\"pure-u-1-1 atomo-slack\">"
        + "Slack emoji: " + slackemoji_list
        + " <button class=\"pure-button\" onClick=\"copyToClipboard('" + slackemoji_list + "');\">Copy Text</button>"
        + "</div>"
        + "<div class=\"pure-u-1-1 atomo-image\">"
        //+ "<img src='img.php?s=" + JSON.stringify(symbolList) + "&w=" + imgWidth + "' alt='"+symbolized_word+"'>"
        + "<img src='api.php?t=image&s=" + JSON.stringify(symbols_list) + "&w=" + imgWidth + "' alt='"+symbolized_word_list+"'>"
        + "</div>"
        + "</div>\n";

    // display the result
    document.getElementById("atomo-spelled-image").innerHTML = spelledImageHTML;

}

function atomoSaveWord(word) {
    var selectedWordList = document.getElementById("atomo-"+word);
    atomoSearchResultJSON [ word ][ "selected" ] = selectedWordList.options[selectedWordList.selectedIndex].value
    atomoSelectedWords[word] = selectedWordList.options[selectedWordList.selectedIndex].value;
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

function copyToClipboard(thisElement) {
    var inputElement = document.getElementById(thisElement);
    inputElement.select();

    try {
      var successful = document.execCommand('copy');
      var msg = successful ? 'successful' : 'unsuccessful';
      console.log('Copying text command was ' + msg);
    } catch (err) {
      console.log('Oops, unable to copy');
    }
}
