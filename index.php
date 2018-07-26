<?php session_start ();
unset($_SESSION['hotel']);
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
				newHtml += createHotel(array[i]);
            }
            document.getElementById("toChange").innerHTML = newHtml;
        }
    };
    anObj.open("GET", "controller.php?todo=getall", true);
    anObj.send(); 
}
function createHotel(hotel){
	var html = "<div class=hotel><div class=hotelTxt>"+hotel["name"]+"&nbsp";
	var tmp = hotel["rating"];
	var i = 0;
	for(i=0;i<5;i++){
		if(tmp>0){
				html+= "★";
			}
		else{
			html+= "☆";
			}
		tmp -= 20;
		}
	html+= "<br><br><a class=link href=\"hotel.php?hotel="+hotel["name"]+"\">Select</a></div>";
	html += "<div class=hotelImg><img src=\"images/"+hotel["name"]+".jpg\" alt=\"Placeholder\"></div>";
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
<link rel="stylesheet" href="styles.css">
</head>
<body>

<p class=title><b>Hotel Reservation</b></p>
<?php
  if( isset(  $_SESSION['curuser'])){
      $str2 = "<a class=link href=\"index.php\" onclick=logout()><u>Logout</u></a>";
      $str1 = "<a class=link href=\"account.php\">My Account</a>";
    echo   "<div id=buttons>".$str2.$str1."</div>";
  }
  else{
      echo "<a class=link href=\"login.php\">Login</a>";
      echo "<a class=link href=\"register.php\">Register</a>";
  }
?>
<br><br><br>


<div id="toChange"></div> 
</body>
</html>