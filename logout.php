<?php
//logs user out, destroys the session
ini_set("session.cookie_httponly", 1);
session_start();
session_destroy();
?>