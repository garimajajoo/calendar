
<?php
ini_set("session.cookie_httponly", 1);
session_start();
require 'database.php';
header("Content-Type: application/json");

$username = $_SESSION['username'];
$send_to_username = $_POST['send_to_username'];
$id = $_POST['id'];
$id=(int)$id;
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
$stmt2 = $mysqli->prepare('SELECT title, event_date, event_time, color from events where event_id = ?');
if(!$stmt2){
	echo "something went wrong...";
	exit();
}
$stmt2->bind_param('i',$id);
$stmt2->execute();
$stmt2->bind_result($title, $date, $time, $color);
$stmt2->fetch();
$stmt2->close();

$stmt = $mysqli->prepare('insert into events(username, title, event_date, event_time, color) values (?,?,?,?, ?)');

if(!$stmt){
	echo "Query Failed. Try again.";
	exit();
}


$stmt->bind_param('sssss', $send_to_username, $title, $date, $time, $color);
$stmt->execute();
$stmt->close();
echo json_encode(array(
        "success" => true,
        ));
        exit;



}


?>