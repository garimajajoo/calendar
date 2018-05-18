<?php
require 'database.php';
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
session_start();
if(!isset($_SESSION['username'])){
	echo json_encode(array(
        "success" => false));
        exit;
}
else{
$username = $_SESSION['username'];
$stmt1 = $mysqli->prepare("select event_id, title, event_date, event_time from events where username = ?");
if(!$stmt1){
echo "Query Failed.";
}

$stmt1->bind_param('s', $username);
$stmt1->execute();
$stmt1->bind_result($id, $title, $event_date, $event_time);
$events=array();
$dates=array();
$times=array();

while($row=$stmt1->fetch()){
$ids[]=$id;
$events[]=htmlentities($title);
$dates[]=$event_date;
$times[]=$event_time;
}
$stmt1->close();

if($events != null){
        echo json_encode(array(
        "success" => true,
        "exist"=>true,
        "id"=>$ids,
        "events" => $events,
        "dates" => $dates,
        "times" => $times));
        exit;
}
else{
        echo json_encode(array(
        "success"=> true,
        "exist"=>false
        ));
        exit;
}
}


?>