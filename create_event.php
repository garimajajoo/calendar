<?php
//this is a relatively simple PHP script that allows people to create an event by inserting it into our database
ini_set("session.cookie_httponly", 1);
session_start();
if(!isset($_SESSION['username'])){
        echo json_encode(array(
                "success" => false,
                "message" => "You were not logged in."
        ));
        exit;
}
$username = $_SESSION['username'];

$title = $_POST['event_title'];
$date = $_POST['date'];
$time = $_POST['time'];
$color = $_POST['color'];
require 'database.php';
$stmt = $mysqli->prepare("insert into events(username, title, event_date, event_time, color) values (?, ?, ?, ?, ?)");
if(!$stmt){
	echo "Query Failed. Sad!";
	exit;
}
$stmt->bind_param('sssss', $username, $title, $date, $time,$color);
$stmt->execute();

$stmt->close();

$stmt2 = $mysqli->prepare("SELECT shared_username from shared_users where calendar_username=?");
if(!$stmt2){
	echo "Query Failed. Oops!";
	exit;
}
$stmt2->bind_param('s', $username);
$stmt2->execute();
$stmt2->bind_result($shared_username);

$users = array();

$i=0;
while($stmt2->fetch()){
	$users[$i]=$shared_username;
	$i++;
}
$stmt2->close();

//here, we are ensuring that the created events goes to all of the calendars that the user has shared to

for($j=0;$j<count($users);$j++){
		$stmt3=$mysqli->prepare('insert into events(username, title, event_date, event_time, color) values (?,?,?,?,?)');
	if(!$stmt3){
		echo "you screwed up...";
		exit();
	}
	$stmt3->bind_param('sssss',$users[$j],$title,$date,$time,$color);
	$stmt3->execute();
	$stmt3->close();
}

echo json_encode(array(
                "success" => true
        ));
        exit;
?>