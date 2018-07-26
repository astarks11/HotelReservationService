<?php session_start ();
?>
<!DOCTYPE html>
<html>
<head>
<script>
update();
function update(){
    var anObj = new XMLHttpRequest();
    anObj.onreadystatechange = function() {
        if (anObj.readyState == 4 && this.status == 200) {
            console.log(anObj.responseText);
        	array = JSON.parse(anObj.responseText);
        	var newHtml = "";
        	for (var i = 0; i < array.length; i++) {
				newHtml += createReservation(array[i]);
            }
            document.getElementById("toChange").innerHTML = newHtml;
        }
    };
    anObj.open("GET", "controller.php?todo=getReservations", true);
    anObj.send(); 
}
function createReservation(reservation){
	var html = "<div class=reservation>";
	html +="Hotel: " + reservation['name'] + "<br>";
	html +="Room Type: " + reservation['type'] + "<br>";
	html +="Start Date: " + reservation['startDate'] + "<br>";
	html +="End Date: " + reservation['endDate'];
	html += "</div>";
	return html;
}
function logout(){
	console.log("logging out");
	var anObj = new XMLHttpRequest();
	anObj.onreadystatechange = function() {
	    if (anObj.readyState == 4 && this.status == 200) {
	    	console.log(anObj.responseText);
	    	document.getElementById("buttons").outerHTML= "";
			
	    }
	};
	anObj.open("GET", "controller.php?todo=logout", true);
	anObj.send();
}
</script>
<meta charset="UTF-8">
<link rel="stylesheet" href="styles.css">
</head>
<body>
<p class=title><b>My Account</b></p>
<a class=link href="index.php">Home</a>
<?php
  if( isset(  $_SESSION['curuser'])){
      $str2 = "<a class=link href=\"index.php\" onclick=logout()><u>Logout</u></a>";
    echo   "<div id=buttons>".$str2."</div>";
  }
  else{
      echo "<a class=link href=\"login.php\">Login</a>";
  }
?>
<br><br><br>

<div id="toChange">temp</div> 
<br>

</body>
</html>