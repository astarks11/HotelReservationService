<?php session_start ();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="styles.css">
</head>
<body>
<p class=title><b>Login</b></p>
<a class=link href="index.php">Home</a>
<a class=link href="register.php">Register</a>
<br><br><br>
<div>
<form class=register action="controller.php" method="POST">
  Username:
  <input type="text" name="user" value="">
  <br>
  Password:
  <input type="password" name="password" value="">
  <br><br>
  <input type="submit" value="Login">
</form> 
</div>
<br>
<?php
  if( isset(  $_SESSION['loginError']))
    echo   $_SESSION['loginError'];
?>
</body>
</html>