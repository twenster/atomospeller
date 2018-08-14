function atomoSpell() {
    word = document.getElementById("word").value;
    len = document.getElementById("len").value;
    httpGetAsync("api.php?word="+word+"&len="+2, function(response) {
        var elements = JSON.parse(response);
        var spelled = "";
        for (var el in elements.component) {
            spelled = spelled + " " + elements.component[el].title;
        }
        spelled = spelled + "<br>\n";
        for (var el in elements.component) {
            spelled = spelled + " <img src=" + elements.component[el].image.url + ">";
        }
        document.getElementById("spelled").innerHTML = spelled;
        console.log(elements);
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
