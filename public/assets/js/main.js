/* 
    App : TRHAL
    Description : project for code.kw ( TRHAL ) 
    About this file : functionality of the trhal site ( main function only ) 
    ajax request in their own page 
    Other : --> i uploaded TRHALCLASSES(v1) on private server 
    { https://trhal-api.com/v1/ }
    use it if you don't have php server
*/
var API_URL = "https://trhal-api.com/v1/";

/* ------------------------------------  Init Page - on load ------------------------------------ */
$(document).ready(function () {
  $("body").addClass("loaded");
  refreshTheme();
});

/* ------------------------------------  Functions ------------------------------------ */

function searchCountry(query) {
  const CountreyName = query;
  if (CountreyName != "") {
    var srchResult = search(CountreyName, countries);
    if (srchResult != undefined) {
      return srchResult;
    } else {
      return false;
    }
  }
}

function switchPanel(avaiablePanels, visbile = null, style = "block") {
  avaiablePanels.forEach((id) => {
    document.getElementById(id).style.display = "none";
  });
  if (visbile != null) {
    document.getElementById(visbile).style.display = style;
  }
}

function ajaxRequest(args, json = true) {
  //Simple Ajax request
  return $.ajax({
    url: API_URL + "/api.php?" + args,
  });
}

function switchTheme(type) {
  if (type == true) {
    //Enable dark mode
    localStorage.setItem("theme", "dark");
  } else {
    //Light mode
    localStorage.setItem("theme", "light");
  }
  refreshTheme();
}

function refreshTheme() {
  const themeMode = localStorage.getItem("theme");
  if (themeMode != null) {
    if (themeMode == "dark") {
      console.log("Theme mode enabled");
      document.documentElement.setAttribute("data-theme", "dark");
      document.getElementById("chk").checked = true;
    } else {
      document.documentElement.setAttribute("data-theme", "light");
      document.getElementById("chk").checked = false;
    }
  }
}
