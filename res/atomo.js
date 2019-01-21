// Globals
var atomoSearchResultJSON = "";
var atomoSelectedWords = [];

function atomoSearch() {
    var word = document.getElementById("atomo-query").value;
    //var length = document.getElementsByName("length").value;

    document.getElementById("atomo-search-result").innerHTML = "";
    document.getElementById("atomo-spelled-image").innerHTML = "";

    httpGetAsync("api.php?t=search&q="+word, atomoSearchResponse);
//    httpGetAsync("api.php?t=search&q="+word+"&l="+length, atomoSearchResponse);
//    httpPostAsync("api.php", "q="+word+"&l="+length, atomoSpellHub());
}

function atomoSearchResponse(response) {
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
        + "<div class=\"atomo-table-select\"><form id=\"table-select\">";

    for (var queryWordId in atomoSearchResultJSON["query_components"]) {

        searchResultHTML += "<div class=\"atomo-select-block\">"
            + "<div class=\"atomo-select-header\">" + queryWordId + "</div>";

        if (atomoSearchResultJSON ["query_components"][ queryWordId ][ queryLength ].length > 0) {
            searchResultFound += 1;

            searchResultHTML += "<div class=\"atomo-select-body\">";

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

                searchResultHTML += "<div><input type=\"radio\" name=\"" + queryWordId + "\" id=\"" + symbol_list + "\" class=\"pure-button\" value=\"" + symbol_list + "\"" + isChecked + "><label for=\"" + symbol_list + "\">" + symbol_label + "</label></div>";

            }

            searchResultHTML += "</div></div>"; // atomo-select-body

        } else {
            searchResultHTML += "<div class=\"atomo-select-body\">No Spelling found</div></div>";
        }
    }
    if ( (searchResultFound>0) )
        searchResultHTML += "<div class=\"atomo-select-button\"><button id=\"atomo-create-button\" class=\"pure-button button-secondary atomo-lookup\">Create image</button></div></div>";

    searchResultHTML += "</div></form></div>"; // block / form: table-select /  div: tomo-table-select


    // display the result
    document.getElementById("atomo-search-result").innerHTML = searchResultHTML;
    atomoAddEventListenerCreateButton();
}

function atomoComposeImage() {
    //var queryLength = "all";
    var imgWidth = 256;
    var slackemoji_list = "";
    var symbols_list = [];
    var spelledImageHTML = "";
    var separator_regex = /-/gi;

    var tableSelect = document.getElementById("table-select"); // form
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

        + "<div class=\"pure-u-1-1 pure-u-sm-1-6 atomo-symbolized\">"
        + "<div class=\"atomo-label\">Symbolized</div>"
        + "</div>"
        + "<div class=\"pure-u-1 pure-u-sm-5-6 atomo-symbolized\">"
        + symbols_list.join(" ")
        + "</div>"

        + "<div class=\"pure-u-1-1 pure-u-sm-1-6 atomo-slack\">"
        + "<div class=\"atomo-label\">Slack emoji</div>"
        + "</div>"
        + "<div class=\"pure-u-1-1 pure-u-sm-5-6 atomo-slack\">"
        + "<input id=\"atomo-slack-" + symbols_list.join(",") + "\" class=\"atomo-slack-input\" type=\“text\“ value=\"" + slackemoji_list + "\">"
        + " <button class=\"pure-button\" onClick=\"copyToClipboard('atomo-slack-" + symbols_list.join(",") + "');\">Copy Text</button>"
        + "</div>"

        + "<div class=\"pure-u-1-1 pure-u-sm-1-6 atomo-image\">"
        + "<div class=\"atomo-label\">Inline</div>"
        + "</div>"
        + "<div class=\"pure-u-1-1 pure-u-sm-5-6 atomo-image\">"
        + "<img src='api.php?t=image&s=" + symbols_list.join(",") + "&w=" + imgWidth + "&d=inline' alt='" + symbols_list.join(",") + "'>"
        + "</div>"

        + "<div class=\"pure-u-1-1 pure-u-sm-1-6 atomo-image\">"
        + "<div class=\"atomo-label\">Left</div>"
        + "</div>"
        + "<div class=\"pure-u-1-1 pure-u-sm-5-6 atomo-image\">"
        + "<img src='api.php?t=image&s=" + symbols_list.join(",") + "&w=" + imgWidth + "&d=left' alt='" + symbols_list.join(",") + "'>"
        + "</div>"
        + "</div>\n";

    // display the result
    document.getElementById("atomo-spelled-image").innerHTML = spelledImageHTML;

}

function httpGetAsync(theUrl, callback) {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.open("GET", theUrl, true); // true for asynchronous

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == XMLHttpRequest.DONE && xmlHttp.status == 200) { // XMLHttpRequest.DONE = 4
            callback(xmlHttp.responseText);
        }
    }

    xmlHttp.send(null);
}

function httpPostAsync(theUrl, theParameters, callback) {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.open("POST", theUrl, true); // true for asynchronous
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == XMLHttpRequest.DONE && xmlHttp.status == 200) { // XMLHttpRequest.DONE = 4
            callback(xmlHttp.responseText);
        }
    }

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

function atomoAddEventListenerSearchQuery() {
    document.getElementById("atomo-query").addEventListener("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            document.getElementById("atomo-search-button").click();
        }
    });
}

function atomoAddEventListenerCreateButton() {
    document.getElementById("atomo-create-button").addEventListener("click", function(event) {
        event.preventDefault();
        atomoComposeImage();
    });
}

// Script is loaded
atomoAddEventListenerSearchQuery();
