<?php session_start ();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lab 2 Your Name</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<p class=title><b>Register</b></p>
<a class=link href="index.php">Home</a>
<a class=link href="login.php">Login</a>
<br><br><br>
<div>
<form class=register action="controller.php" method="POST">
  First Name:
  <input type="text" name="firstName" value="">
  <br>
  Last Name:
  <input type="text" name="lastName" value="">
  <br>
  Username:
  <input type="text" name="username" value="">
  <br>
  Password:
  <input type="password" name="password" value="">
  <br><br>
  <input type="submit" value="Register">
</form> 
</div><br>
<?php
  if( isset(  $_SESSION['registerError']))
    echo   $_SESSION['registerError'];
?>
</body>
</html>