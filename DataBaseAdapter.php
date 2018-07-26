<?php
session_start ();
class DatabaseAdaptor {
    // The instance variable used in every one of the functions in class DatbaseAdaptor
    private $DB;
    
    // Make a connection to an existing data based named 'first' that has table customer
    public function __construct() {
        $db = 'mysql:dbname=final; charset=utf8; host=127.0.0.1';
        $user = 'root';
        $password = '';
        
        try {
            $this->DB = new PDO ( $db, $user, $password );
            $this->DB->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch ( PDOException $e ) {
            echo ('Error establishing Connection');
            exit ();
        }
    }
    public function getAllHotels() {
        $stmt = $this->DB->prepare ( "SELECT * FROM hotel order by rating desc" );
        $stmt->execute ();
        return $stmt->fetchAll ( PDO::FETCH_ASSOC );
    }
    public function getRooms($name){
        $name = htmlspecialchars($name);
        $stmt = $this->DB->prepare ("
            select hotel.name,hotel.rating, rooms.type, rooms.roomID  from rooms 
            join hotel on hotel.hotelID = rooms.hotelID 
            where hotel.name = :name
            ");
        $stmt->bindParam('name', $name);
        $stmt->execute ();
        return $stmt->fetchAll ( PDO::FETCH_ASSOC );
    }
    

    public function reserve($hotelName, $roomID, $start, $end){
        $hotelName = htmlspecialchars($hotelName);
        $roomID = htmlspecialchars($roomID);
        $start = htmlspecialchars($start);
        $end = htmlspecialchars($end);
    	if(!isset($_SESSION['curuser'])){
    		$_SESSION['reserveError'] = "Error, you must be logged in to try to reserve a room";
    	} else {
    		$_SESSION['reserveError'] = "";
    	}
    	// check if room is already reserved
    	//----------------- isReserved()-----------------------------------
    	$command2 = "select * from hotel where name = :hotelName";
    	$stmt1 = $this->DB->prepare ($command2);
    	$stmt1->bindParam('hotelName', $hotelName);
    	$stmt1->execute ();
    	$hotelId1 = ($stmt1->fetchAll ( PDO::FETCH_ASSOC ))[0]['hotelID'];
    	
    	$command1 = "select * from reservation where hotelID = :hotelId and roomID = :roomId";    	
    	$stmt = $this->DB->prepare ($command1);
    	$stmt->bindParam('hotelId', $hotelId1);
    	$stmt->bindParam('roomId', $roomID);
    	$stmt->execute ();
    	$temp1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	
    	// loop through reservation dates of given hotel and room
    	foreach ($temp1 as $item) {
    		$startDate = $item['startDate'];
    		$endDate = $item['endDate'];
    		// check if given date is between booked dates
    		if ($start >= $startDate && $start <= $endDate ) {
    			$_SESSION['reserveError'] .= "ALREADY BOOKED";
    			return;
    		}
    		// check if end date is between booked dates
    		if ($end >= $startDate && $end <= $endDate) {
    			$_SESSION['reserveError'] .= "ALREADY BOOKED";
    			return;
    		}
    		// check if given dates have booked dates between
    		if ($start <= $startDate && $end >= $endDate) {
    			$_SESSION['reserveError'] .= "ALREADY BOOKED";
    			return;
    		}
    		//$_SESSION['reserveError'] .= "<br>start:".$startDate . " end:".$endDate; //debugging
    	}
    	//return; //debugging
    	//----------------- isReserved()---------------------------------
    	    	
        if ((date("Y-m-d") > $start) == 1 ) {
        	$_SESSION['reserveError'] = "Error, start date must be greater than current date";
        }
        else if (($start <= $end) != 1) {
        	$_SESSION['reserveError'] = "Error, end date must be greater or equal to start date";
        }
        else if(empty($start)){
            $_SESSION['reserveError'] = "Dates not selected";
        }
        else {   
            //not reserved
            $command1 = "select hotelID from hotel where name = :hotelName";
            $stmt = $this->DB->prepare ($command1);
            $stmt->bindParam('hotelName', $hotelName);
            $stmt->execute ();
            $hotelId1 = ($stmt->fetchAll ( PDO::FETCH_ASSOC ))[0]['hotelID']; 
            
            $command2 = "select userID from users where uname = :curuser";
            $stmt = $this->DB->prepare ($command2);
            $stmt->bindParam('curuser', $_SESSION['curuser']);
            $stmt->execute ();
            $user = ($stmt->fetchAll ( PDO::FETCH_ASSOC ))[0]['userID']; 
            
            $command = "insert into reservation(hotelID, roomID, userID, startDate, endDate) values (:hotelId,:roomId,:user,:start,:end)";
            $stmt = $this->DB->prepare ($command);
            $stmt->bindParam('hotelId', $hotelId1);
            $stmt->bindParam('roomId', $roomID);
            $stmt->bindParam('user', $user);
            $stmt->bindParam('start', $start);
            $stmt->bindParam('end', $end);
            $stmt->execute ();
            return $command;
        }
    }
    public function getReservations(){
        $command1 = "select userID from users where uname = '".$_SESSION['curuser']."'";
        $stmt = $this->DB->prepare ($command1);
        $stmt->execute ();
        $user = ($stmt->fetchAll ( PDO::FETCH_ASSOC ))[0]['userID'];
        
        $command = "select reservation.startDate, endDate, hotel.name, rooms.type 
                    from reservation join hotel on hotel.hotelID = reservation.hotelID 
                    join rooms on rooms.roomID = reservation.roomID where userID=".$user;
        $stmt = $this->DB->prepare ($command);
        $stmt->execute ();
        $result = $stmt->fetchAll ( PDO::FETCH_ASSOC );
        return $result;
    }
    public function checkUser($name){
        $name = htmlspecialchars($name);
        //$command = "select exists(select * from users where username = \"".$name."\")";
        $command = "select userID from users where uname = :name";
        $stmt = $this->DB->prepare ($command);
        $stmt->bindParam('name', $name);
        $stmt->execute ();
        $result = $stmt->fetchAll ( PDO::FETCH_ASSOC ); 
        return (count($result));
    }
    public function addUser($firstName, $lastName, $username, $hash){
        $firstName = htmlspecialchars($firstName);
        $lastName = htmlspecialchars($lastName);
        $username = htmlspecialchars($username);
        $hash = htmlspecialchars($hash);
        $command = "insert into users(fname, lname, uname, password) values (:firstName,:lastName,:userName,:hash)";
        $stmt = $this->DB->prepare ($command);
        $stmt->bindParam('firstName', $firstName);
        $stmt->bindParam('lastName', $lastName);
        $stmt->bindParam('userName', $username);
        $stmt->bindParam('hash', $hash);
        $stmt->execute ();
        return $command;
    }
    public function getHash($name){
        $name = htmlspecialchars($name);
        $command = "select password from users where uname = :name";
        $stmt = $this->DB->prepare ($command);
        $stmt->bindParam('name', $name);
        $stmt->execute ();
        return $stmt->fetchAll ( PDO::FETCH_ASSOC );
    }
} // End class DatabaseAdaptor

$theDBA = new DatabaseAdaptor();
// Do not put any other echo, print, or print_r here.

?>