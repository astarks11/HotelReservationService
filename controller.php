<?php
//session_start ();  //sesion already started in databaseadapter

include 'DataBaseAdapter.php';

if(isset($_POST['quote'])  && isset($_POST['author'])) {
    //TODO check if string is empty
    if(!empty($_POST['quote'])&&!empty($_POST['author'])){
        $arr = $theDBA->addQuote($_POST['quote'],$_POST['author']);
        unset($_SESSION['quoteError']);
        header('Location: index.php');
        return;
    }
    $_SESSION['quoteError'] = "Invalid quote.Please fill both fields";
    header('Location: quotes.php');
    
}
if(isset($_POST['username'])  && isset($_POST['password'])) {   //registering
    //TODO check if string is empty
    if(empty($_POST['username'])||empty($_POST['password'])){
        $_SESSION['registerError'] = 'Invalid credentials.Please fill both fields';
        header('Location: register.php');
        return;
    }
    else if(($theDBA->checkUser($_POST['username']))!= 0){
        $_SESSION['registerError'] = 'Invalid credentials. User already exists';
        header('Location: register.php');
    }
    else{
        unset($_SESSION['registerError']);
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $arr = $theDBA->addUser($_POST['firstName'],$_POST['lastName'],$_POST['username'],$hash);
        header('Location: index.php');
    }
}
if(isset($_POST['user'])  && isset($_POST['password'])) {   //login
    //TODO check if string is empty
    if(($theDBA->checkUser($_POST['user']))== 0){       //check if the user exists
        $_SESSION['loginError'] = 'Invalid credentials. User doesn\'t exists';
        header('Location: login.php');
        return;
    }
    $hash = ($theDBA->getHash($_POST['user']))[0]['password'];
    $is_verified = password_verify($_POST['password'], $hash);
    if($is_verified != 1){
        $_SESSION['loginError'] = 'Invalid credentials.'.$hash;
        header('Location: login.php');
        return;
    }
    else{
        unset($_SESSION['loginError']);
        $_SESSION['curuser'] = $_POST['user'];
        header('Location: index.php');
    }
    //header('Location: index.php');
}
if(isset($_POST['startDate'])  && isset($_POST['endDate'])) { //submitting date reservation request
    $theDBA->reserve($_SESSION['hotel'], $_GET['room'], $_POST['startDate'], $_POST['endDate']);
    header('Location: hotel.php');
}
if(isset($_GET['todo'])) {
    if($_GET['todo']=="getall"){
        $arr = $theDBA->getAllHotels();
        echo json_encode($arr);
    }
    else if($_GET['todo']=="getRooms"){
        if( isset($_SESSION['hotel'])){
            $arr = $theDBA->getRooms($_SESSION['hotel']);
            echo json_encode($arr);
        }
        else{
            echo "error";
        }
    }
    else if($_GET['todo']=="getReservations"){
        if( isset($_SESSION['curuser'])){
            $arr = $theDBA->getReservations();
            echo json_encode($arr);
        }
        else{
            echo "error";
        }
    }
    else if(substr($_GET['todo'],0,4)=="logo"){
        session_unset();
        header('Location: index.php');
        //echo "logged out";
    }
    else{
        echo"error".":".$_GET['todo'];
    }
}
?>