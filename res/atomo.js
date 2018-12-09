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
    var word = document.getElementById("word").value;
    var length = document.getElementsByName("length").value;

    document.getElementById("atomo-search-result").innerHTML = "";
    document.getElementById("atomo-spelled-image").innerHTML = "";

    httpGetAsync("api.php?t=search&q="+word+"&l="+length, atomoSpellResponse);
//    httpPostAsync("api.php", "q="+word+"&l="+length, atomoSpellHub());
}

function atomoSpellResponse(response) {
    atomoSearchResultJSON = JSON.parse(response);
    var queryLength = "all";
    var searchResultHTML = "";
    var searchResultFound = 0;

    // prepare global variable
    for (var queryWordId in atomoSearchResultJSON["query_components"]) {
        atomoSelectedWords[queryWordId] = "";
    }

    // table header
    searchResultHTML = "<div class=\"pure-u-1-1 atomo-select\">"
        + "<p>Each word may have different spellings, select your prefered word to compose your sentence</p>"
        + "<div class=\"atomo-table-select\"><form id=\"table-select\"><table class=\"pure-table\"><thead><tr>";
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

            //searchResultHTML +=
            //    "<td><select id=\"atomo-" + queryWordId + "\" class=\"atomo-select\" size=\"5\" onChange=\"atomoSaveWord('"+queryWordId+"')\">";

            searchResultHTML +=
                "<td>";
            for (spellingsId   in atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength] ) {
                symbolized_word = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbolized_word" ];
                symbolized_word_shortest = atomoSearchResultJSON ["query_components"][ queryWordId ][ "shortest" ][ 0 ][ "symbolized_word" ];

                // If there is only one symbols, you can't join the array !
                if (atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].length > 1) {
                    symbol_label = "<div class=\"atomo-symbol\">" + atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].join("</div> <div class=\"atomo-symbol\">") + "</div>";
                    symbol_list = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ].join("-");
                } else {
                    symbol_label = "<div class=\"atomo-symbol\">" + atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ] + "</div>";
                    symbol_list = atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ][ spellingsId ][ "symbols" ];
                }

                isChecked = (symbolized_word == symbolized_word_shortest) ? " checked" : "";
                //searchResultHTML +=
                //    "<option class=\"pure-button\" value=\"" + symbolized_word + "\">" + symbol_list + "</option>";
                searchResultHTML +=
                    "<div><input type=\"radio\" name=\"" + queryWordId + "\" id=\"" + symbol_list + "\" class=\"pure-button\" value=\"" + symbol_list + "\"" + isChecked + "><label for=\"" + symbol_list + "\">" + symbol_label + "</label></div>";

            }
            //searchResultHTML +=
            //    "</select></td>";
            searchResultHTML +=
                "</td>";

        } else {
            searchResultHTML +=
                "<td>No Spelling found</td>";
        }
    }
    searchResultHTML +=
        "</tr></tbody></table></form></div>";

    if ( (searchResultFound>0) )
        searchResultHTML +=
            "<div class=\"atomo-table-button\"><button id=\"atomo-create\" class=\"pure-button button-secondary atomo-lookup\" onClick=\"atomoComposeImage()\">Create image</button></div></div>";


    // display the result
    document.getElementById("atomo-search-result").innerHTML = searchResultHTML;
}

function atomoComposeImage() {
    //var queryLength = "all";
    var imgWidth = 256;
    var slackemoji_list = "";
    var symbols_list = [];
    var spelledImageHTML = "";
    var separator_regex = /-/gi;

    var tableSelect = document.getElementById("table-select");
    var tableData = new FormData(tableSelect);

    for (const tableDataItem of tableData) {
        queryWordId = tableDataItem[0];
        queryWordSelected = tableDataItem[1];

        slackemoji_list = slackemoji_list + " " + (':atomo' + queryWordSelected.replace(separator_regex, ': :atomo') + ':').toLowerCase();
        symbols_list = symbols_list.concat(queryWordSelected);

    };

    slackemoji_list = slackemoji_list.trim();

    spelledImageHTML +=
          "<div class=\"pure-u-1-1 atomo-result\">"

        + "<div class=\"pure-u-1-6 atomo-symbolized\">"
        + "<div class=\"atomo-label\">Symbolized</div>"
        + "</div>"
        + "<div class=\"pure-u-5-6 atomo-symbolized\">"
        + symbols_list
        + "</div>"

        + "<div class=\"pure-u-1-6 atomo-slack\">"
        + "<div class=\"atomo-label\">Slack emoji</div>"
        + "</div>"
        + "<div class=\"pure-u-5-6 atomo-slack\">"
        + "<input id=\"atomo-slack-" + symbols_list + "\" class=\"atomo-slack-input\" type=\“text\“ value=\"" + slackemoji_list + "\">"
        + " <button class=\"pure-button\" onClick=\"copyToClipboard('atomo-slack-" + symbols_list + "');\">Copy Text</button>"
        + "</div>"

        + "<div class=\"pure-u-1-6 atomo-image\">"
        + "<div class=\"atomo-label\">Inline</div>"
        + "</div>"
        + "<div class=\"pure-u-5-6 atomo-image\">"
        + "<img src='api.php?t=image&s=" + symbols_list + "&w=" + imgWidth + "&d=inline' alt='"+symbols_list+"'>"
        + "</div>"

        + "<div class=\"pure-u-1-6 atomo-image\">"
        + "<div class=\"atomo-label\">Left</div>"
        + "</div>"
        + "<div class=\"pure-u-5-6 atomo-image\">"
        + "<img src='api.php?t=image&s=" + symbols_list + "&w=" + imgWidth + "&d=left' alt='"+symbols_list+"'>"
        + "</div>"
        + "</div>\n";

    // display the result
    document.getElementById("atomo-spelled-image").innerHTML = spelledImageHTML;

}

function atomoSaveWord(word) {
    var selectedWordList = document.getElementById("atomo-"+word);
    //var createButton = document.getElementById("atomo-create");
    atomoSearchResultJSON [ "query_components" ][ word ][ "selected" ] = selectedWordList.options[selectedWordList.selectedIndex].value
    atomoSelectedWords [word] = selectedWordList.options[selectedWordList.selectedIndex].value;
    //createButton.classList.remove("pure-button-disabled");
}

function httpGetAsync(theUrl, callback) {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == XMLHttpRequest.DONE && xmlHttp.status == 200) { // XMLHttpRequest.DONE = 4
            callback(xmlHttp.responseText);
        }
    }

    xmlHttp.open("GET", theUrl, true); // true for asynchronous
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
