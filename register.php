<?php
//Registers the user. Most of the code is identical to how we registered a user in previous modules, the only difference being our usage of JSON

header("Content-Type: application/json");
require "database.php";
$username=$_POST['username'];
$password=$_POST['password'];
	$stmt1=$mysqli->prepare("select count(*) from users where username =?");
	if(!$stmt1){
	echo "Query Failed";
	echo "<form action=calendar.html method='POST'>
	<input type = 'submit' value = 'Click here to return to home page'/>
	</form>";
	exit;
	}
	$stmt1->bind_param('s',$username);
	$stmt1->execute();
	$stmt1->bind_result($count);
	$stmt1->fetch();
	$stmt1->close();
	if($count==1){
	echo json_encode(array(
		"success" => false,
		"message" => "This username already exists. Please pick another one."
		//JSON communicating with JavaScript that the username typed in already exists
	));
	exit;
	}



	else{
		$stmt=$mysqli->prepare("INSERT INTO users (username,password) values (?,?)");
		if(!$stmt){
			echo "failed";
			echo "<form action=calendar.html method='POST'>
			<input type = 'submit' value = 'Click here to return to home page'/>
			</form>";
			exit;
	}
	$pwd_hash=password_hash($password, PASSWORD_BCRYPT);
	$stmt->bind_param('ss',$username,$pwd_hash);
	$stmt->execute();
	$stmt->close();
	ini_set("session.cookie_httponly", 1);
	session_start();
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
	$_SESSION['username'] = $username;
	echo json_encode(array(
		"success" => true
		//Successful registration!
	
	));
	exit;
	}
?>
