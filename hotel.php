<?php
include 'DataBaseAdapter.php';

if(isset($_GET['hotel'])){
    $_SESSION['hotel'] = $_GET['hotel'];
}
?>
<!DOCTYPE html>
<html>
<head>
<script>
getRooms();
function getRooms(){
	var anObj = new XMLHttpRequest();
    anObj.onreadystatechange = function() {
        if (anObj.readyState == 4 && this.status == 200) {
            console.log(anObj.responseText);
        	array = JSON.parse(anObj.responseText);
        	var newHtml = "";
        	for (var i = 0; i < array.length; i++) {
				newHtml += "<div class=room>"
		        newHtml += createRoom(array[i]);
		        newHtml += createForm(array[i]["roomID"]);
		        newHtml += "</div>";
            }
            document.getElementById("toChange").innerHTML = newHtml;
        }
    };
    anObj.open("GET", "controller.php?todo=getRooms", true);
    anObj.send(); 
}
function createRoom(room){
return "Room Type:"+ room['type'];
}
function createForm(roomID){
	console.log(roomID);
	html = "";
	html += "<form action=\"controller.php?room="+roomID+"\" method=\"POST\">";
	html += "Start Date:";
	html += "<input type=\"date\" name=\"startDate\" value=\"\">";
	html += "<br>";
	html += "End Date:";
	html += "<input type=\"date\" name=\"endDate\" value=\"\">";
	html += "<input type=\"submit\" value=\"submit\">";
	html += "</form>";
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
<?php 
if (isset( $_SESSION['hotel'])){
    echo "<p class=title><b>".$_SESSION['hotel']." Room Selection</b></p>";
}
if (isset( $_SESSION['return'])){
    echo $_SESSION['return'];
}
?>
<a class=link href="index.php">Home</a>
<?php
  if( isset(  $_SESSION['curuser'])){
      $str2 = "<a class=link href=\"index.php\" onclick=logout()><u>Logout</u></a>";
    echo   "<div id=buttons>".$str2."</div>";
    echo "<a class=link href=\"account.php\">My Account</a>";
  }
  else{
      echo "<a class=link href=\"login.php\">Login</a>";
      echo "<a class=link href=\"register.php\">Register</a>";
  }
?>
<br><br><br>
<?php 
    if (isset( $_SESSION['reserveError'])){
    echo $_SESSION['reserveError'];
    unset($_SESSION['reserveError']);
}?>
<div id=toChange>

</div>
<br>
</body>
</html>
