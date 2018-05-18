<?php
require 'database.php';
//this sciprt allows us to delete events from the database...first, we pull the ID from the database, then we use that ID to delete that particular event
ini_set("session.cookie_httponly", 1);
session_start();
$username = $_SESSION['username'];
$id = (int)$_POST['id'];

$stmt1 = $mysqli->prepare("select title, event_date, event_time, color from events where (username=? and event_id=?)");
if(!$stmt1){
	echo "Query Failed. You suck. Try again later.";
	exit();
}

$stmt1->bind_param('si', $username, $id);
$stmt1->execute();
$stmt1->bind_result($title,$date,$time,$color);
$stmt1->fetch();
echo "<p>".$safe."</p>\n";
$stmt1->close();

//this is where we pull the ID from the database

$stmt = $mysqli->prepare("delete from events WHERE (username = ? AND event_id = ?)");
if(!$stmt){
	echo "Query Failed. Sorry! Try again later.";
	exit();
}

$stmt->bind_param('si', $username, $id);
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


//this is where the deletion happens
for($j=0;$j<count($users);$j++){
		$stmt3=$mysqli->prepare('delete from events where (username = ? AND title=? AND event_date=? AND event_time = ? AND color =?)');
	if(!$stmt3){
		echo "you screwed up...";
		exit();
	}
	$stmt3->bind_param('sssss',$users[$j],$title,$date,$time,$color);
	$stmt3->execute();
	$stmt3->close();
}

?>