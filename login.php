<?php
//allows the user to login. this is a relatively simple PHP script, the only new addition is the JSON

header("Content-Type: application/json");
require "database.php";

$username=$_POST['username'];
$password=$_POST['password'];


        $stmt=$mysqli->prepare("select count(*),username, password from users where username=?");

        if(!$stmt){
        echo "failed";
        exit;
}

$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($cnt,$user_stored,$pwd_hash);
$stmt->fetch();
$stmt->close();

if($cnt==1 && password_verify($password,$pwd_hash)){
ini_set("session.cookie_httponly", 1);
session_start();
        $_SESSION['username'] = $username;
        $_SESSION['token'] = substr(md5(rand()), 0, 10);
		echo json_encode(array(
                "success" => true));
				exit;
				//JSON to communicate to JavaScript that login is successful


}
else{

        echo json_encode(array(
                "success" => false,
                "message" => "Incorrect Username or Password"
        ));
        exit;

}



?>