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
    var imgWidth = atomoSearchResultJSON["atomo_settings"]["source_element_width"];
    var searchResultHTML = "";
    var searchResultFound = 0;

    // prepare global variable
    for (var queryWordId in atomoSearchResultJSON["query_components"]) {
        atomoSelectedWords[queryWordId] = "";
    }

    // table header
    searchResultHTML = "<div class=\"pure-u-1-1\">"
        + "<p>Each word may have different spelling, select your prefered word to compose you sentence</p>"
        + "<div class=\"atomo-table-select\"><table class=\"pure-table\"><thead><tr>";
    for (var queryWordId in atomoSearchResultJSON["query_components"]) {
        searchResultHTML +=
            "<th>" + queryWordId + "</th>";
    }
    searchResultHTML +=
        "</tr></thead>";

    // table row
    searchResultHTML +=
        "<tbody><tr>";
    for (var queryWordId in atomoSearchResultJSON["query_components"]) {
        if (atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ].length > 0) {
            searchResultFound += 1;
            searchResultHTML +=
                "<td><select id=\"atomo-" + queryWordId + "\" class=\"atomo-select\" size=\"5\" onChange=\"atomoSaveWord('"+queryWordId+"')\">";
            for (spellingsId   in atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength] ) {
                symbolized_word = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbolized_word" ];

                // If there is only one symbols, you can't join the array !
                if (atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].length > 1) {
                    symbol_list = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].join("-");
                } else {
                    symbol_list = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ];
                }

                searchResultHTML +=
                    "<option class=\"pure-button\" value=\"" + symbolized_word + "\">" + symbol_list + "</option>";
            }
            searchResultHTML +=
                "</select></td>";
        } else {
            searchResultHTML +=
                "<td>No Spelling found</td>";
        }
    }
    searchResultHTML +=
        "</tr></table></div>";

    if ( (searchResultFound>0) )
        searchResultHTML +=
            "<div class=\"atomo-table-button\"><button class=\"pure-button atomo-lookup\" onClick=\"atomoComposeImage()\">Create image</button></div></div>";


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
    for (var queryWordId in atomoSearchResultJSON["query_components"]) {
        symbolized_word_list +=
            atomoSearchResultJSON["query_components"][ queryWordId ][ "selected" ] + " ";

        for (var all_symbolized in atomoSearchResultJSON["query_components"][ queryWordId ][ queryLength ]) {
            if (atomoSearchResultJSON [ "query_components" ][ queryWordId ][ queryLength ] [ all_symbolized ][ "symbolized_word" ] == atomoSearchResultJSON["query_components"][ queryWordId ][ "selected" ]) {
                slackemoji_list = slackemoji_list + " " + (':atomo' + atomoSearchResultJSON [ "query_components" ][ queryWordId ][ queryLength ] [ all_symbolized ][ "symbols" ].join(': :atomo') + ':').toLowerCase();
                symbols_list = symbols_list.concat(atomoSearchResultJSON["query_components"][ queryWordId ][ queryLength ] [ all_symbolized ][ "symbols" ]);
            }
        }
    }

    spelledImageHTML +=
          "<div class=\"pure-u-1-1 atomo-result\">"
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
    atomoSearchResultJSON [ "query_components" ][ word ][ "selected" ] = selectedWordList.options[selectedWordList.selectedIndex].value
    atomoSelectedWords [word] = selectedWordList.options[selectedWordList.selectedIndex].value;
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
