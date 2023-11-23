<?php include 'functions.php';

displayErrors();

$username = "yct482";
$password = "kb3r7XBL4G9F6pdh";
$data = "username=".$username."&password=".$password;

$result = clearSID($data, $username);

$cinfo = json_decode($result, true);
echo "<pre>";
echo print_r($cinfo);
echo "</pre>";
?>