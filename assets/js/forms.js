/* 
    App : TRHAL
    Description : project for code.kw ( TRHAL ) 
    About this file : Simple Forms Control Heart 
    Other : --> Everything created from scratch
    Ofcourse with some help ( sources : google - w3s - csstricks )
    and last but not least ( app.code.kw )
*/

/* ---------------- Country Auto Complete (W3S Shcool) ---------------- */
function autocomplete(inp, arr, imgFocus = null) {
  var currentFocus;
  inp.addEventListener("input", function (e) {
    var a,
      b,
      i,
      val = this.value;
    closeAllLists();
    if (!val) {
      if (imgFocus != null) {
        document.getElementById(imgFocus).src = "";
      }
      return false;
    }
    currentFocus = -1;
    a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");
    this.parentNode.appendChild(a);
    for (i = 0; i < arr.length; i++) {
      if (arr[i]["name"].toString().substr(0, val.length).toUpperCase() == val.toUpperCase()) {
        b = document.createElement("DIV");
        b.innerHTML = "<img style='width: 22px; margin-left:5px;' src='./assets/flags/" + arr[i]["code"] + ".png'><strong>" + arr[i]["name"].substr(0, val.length) + "</strong>";
        b.innerHTML += arr[i]["name"].substr(val.length);
        b.innerHTML += "<input type='hidden' data-code=" + arr[i]["code"] + " value='" + arr[i]["name"] + "'>";
        b.addEventListener("click", function (e) {
          inp.value = this.getElementsByTagName("input")[0].value;
          inp.setAttribute("data-code", this.getElementsByTagName("input")[0].getAttribute("data-code"));
          if (imgFocus != null) {
            document.getElementById(imgFocus).src = "./assets/flags/" + this.getElementsByTagName("input")[0].getAttribute("data-code") + ".png";
          }
          closeAllLists();
        });
        a.appendChild(b);
      } else {
        if (imgFocus != null) {
          document.getElementById(imgFocus).src = "";
        }
  
      }
    } 
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      currentFocus++;
      addActive(x);
    } else if (e.keyCode == 38) {
      currentFocus--;
      addActive(x);
    } else if (e.keyCode == 13) {
      e.preventDefault();
      if (currentFocus > -1) {
        if (x) x[currentFocus].click();
      }
    }
  });
  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = x.length - 1;
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });
}

function search(nameKey, myArray) {
  for (var i = 0; i < myArray.length; i++) {
    if (myArray[i].name === nameKey) {
      return myArray[i];
    }
  }
}

function searchByType(nameKey, myArray) {
  for (var i = 0; i < myArray.length; i++) {
    if (myArray[i].type === nameKey) {
      return myArray[i];
    }
  }
}

function searchByCode(nameKey, myArray) {
  for (var i = 0; i < myArray.length; i++) {
    if (myArray[i].code.toUpperCase() === nameKey.toUpperCase()) {
      return myArray[i];
    }
  }
}


/* ---------------- Animate number in any html element using regex ---------------- */

function animateValue(obj, start = 0, end = null, duration = 3000) {
  if (obj) {
    var textStarting = obj.innerHTML;
    end = end || parseInt(textStarting.replace(/\D/g, ""));
    var range = end - start;
    var minTimer = 50;
    var stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);
    var startTime = new Date().getTime();
    var endTime = startTime + duration;
    var timer;
    function run() {
      var now = new Date().getTime();
      var remaining = Math.max((endTime - now) / duration, 0);
      var value = Math.round(end - remaining * range);
      obj.innerHTML = textStarting.replace(/([0-9]+)/g, value);
      if (value == end) {
        clearInterval(timer);
      }
    }

    timer = setInterval(run, stepTime);
    run();
  }
}



function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;
  while (0 !== currentIndex) {
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }
  return array;
}

function parseURL(url) {
  var parser = document.createElement('a'),
    searchObject = {},
    queries, split, i;
  // Let the browser do the work
  parser.href = url;
  // Convert query string to object
  queries = parser.search.replace(/^\?/, '').split('&');
  for (i = 0; i < queries.length; i++) {
    split = queries[i].split('=');
    searchObject[split[0]] = split[1];
  }
  return {
    protocol: parser.protocol,
    host: parser.host,
    hostname: parser.hostname,
    port: parser.port,
    pathname: parser.pathname,
    search: parser.search,
    searchObject: searchObject,
    hash: parser.hash
  };
}
