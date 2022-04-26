var x = document.getElementById("graphs").style.display = "none";

function hideGraphs() {
    var x = document.getElementById("graphs");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }