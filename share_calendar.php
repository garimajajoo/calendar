<?php
//PHP script for creative portion to allow a user to share his/her calendar with another user
//This script takes in a username, makes sure that it exists in the database. Then, the calendar of the original user is taken from the database. 
//Finally, the calendar is inserted into the second user's calendar by inserting all events into their database.  
ini_set("session.cookie_httponly", 1);
session_start();
if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}
require 'database.php';
header("Content-Type: application/json");
$send_to_username=$_POST['send_to_username'];
$username = $_SESSION['username'];

$stmt1 = $mysqli->prepare('SELECT count(*) from users where username = ?');
if(!$stmt1){
	echo "First Query Failed. So did you.";
	exit();
}
$stmt1->bind_param('s', $send_to_username);
$stmt1->execute();
$stmt1->bind_result($count);
$stmt1->fetch();
$stmt1->close();
if($count == 0){
	echo json_encode(array(
        "success" => false));
        exit;
}

else{
$stmt2 = $mysqli->prepare('SELECT title, event_date, event_time, color from events where username = ?');
if(!$stmt2){
	echo "something went wrong...";
	exit();
}
$stmt2->bind_param('s',$username);
$stmt2->execute();
$stmt2->bind_result($title, $date, $time, $color);

$titles = array();
$dates = array();
$times = array();
$colors = array();

$i=0;
while($stmt2->fetch()){
	$titles[$i]=$title;
	$dates[$i]=$date;
	$times[$i]=$time;
	$colors[$i]=$color;
	$i++;
}
$stmt2->close();



for($j=0; $j<count($titles); $j++){
	$stmt3=$mysqli->prepare('insert into events(username, title, event_date, event_time, color) values (?,?,?,?,?)');
	if(!$stmt3){
		echo "you screwed up...";
		exit();
	}
	$stmt3->bind_param('sssss',$send_to_username,$titles[$j],$dates[$j],$times[$j], $colors[$j]);
	$stmt3->execute();
	$stmt3->close();
}

$stmt4 = $mysqli->prepare('INSERT INTO shared_users(calendar_username, shared_username) values (?,?)');
if(!$stmt4){
		echo "Query Failed...";
		exit();
	}
$stmt4->bind_param('ss',$username, $send_to_username);
$stmt4->execute();
$stmt4->close();

echo json_encode(array(
        "success" => true));
        exit;

}


?>