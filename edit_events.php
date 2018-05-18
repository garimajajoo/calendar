<?php
//allows us to edit events from the user. two steps – first, we make the edit; second, we make sure that every shared calendar receives the edit
ini_set("session.cookie_httponly", 1);
session_start();
require "database.php";
$username = $_SESSION['username'];
$id=$_POST['id'];
$id=(int)$id;


$new_title=$_POST['new_title'];
$new_date=$_POST['new_date'];
$new_time = $_POST['new_time'];
$new_color = $_POST['new_color'];

$stmt1 = $mysqli->prepare("select title, event_date, event_time, color from events where (username=? and event_id=?)");
if(!$stmt1){
	echo "Query Failed. You suck. Try again later.";
	exit();
}

$stmt1->bind_param('si', $username, $id);
$stmt1->execute();
$stmt1->bind_result($title,$date,$time,$color);
$stmt1->fetch();
$stmt1->close();

//here's where we make the initial update

$stmt = $mysqli->prepare("UPDATE events SET title = ?, event_date = ?, event_time = ?, color = ? WHERE (username = ? AND event_id = ?)");
if(!$stmt){
	echo "Query Failed. Sorry. Try again later.";
	exit();
}
$stmt->bind_param('sssssi', $new_title, $new_date, $new_time, $new_color, $username, $id);

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

//here's where we update the other calendars that the event / user has shared to

for($j=0;$j<count($users);$j++){
		$stmt3=$mysqli->prepare('UPDATE events SET title = ?, event_date = ?, event_time =?, color =? where (username = ? AND title=? AND event_date=? AND event_time = ? AND color =?)');
	if(!$stmt3){
		echo "you screwed up...";
		exit();
	}
	$stmt3->bind_param('sssssssss',$new_title, $new_date, $new_time, $new_color, $users[$j], $title, $date, $time, $color);
	$stmt3->execute();
	$stmt3->close();
}


?>