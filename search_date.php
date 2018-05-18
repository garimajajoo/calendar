<?php
//This PHP script helps us to search through all the events 

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
$date=$_POST['date'];
$stmt= $mysqli->prepare("select title, event_time, color from events where (username = ? and event_date=?)");
if(!$stmt){
echo "Query Failed.";
}
$stmt->bind_param('ss', $username,$date);
$stmt->execute();
$stmt->bind_result($title, $event_time,$color);

$events=array();
$times=array();
$colors=array();

while($stmt->fetch()){
$events[]=htmlentities($title);
$times[]=$event_time;
$colors[]=$color;
}
$stmt->close();

if($events != null){
        echo json_encode(array(
        "success" => true,
        "exist"=>true,
        "events" => $events,
        "times" => $times,
        "colors"=>$colors));
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