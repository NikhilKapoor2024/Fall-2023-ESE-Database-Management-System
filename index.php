<?php include 'functions.php';

displayErrors();

// creating data variable to hold username and password as a string
$username = "yct482";
$password = "kb3r7XBL4G9F6pdh";
$data = "username=".$username."&password=".$password;

// initializing a session from a cURL
$result = createSession($data);
$cinfo = json_decode($result, true);
echo "<pre>";
echo print_r($cinfo);
echo "</pre>";
?>