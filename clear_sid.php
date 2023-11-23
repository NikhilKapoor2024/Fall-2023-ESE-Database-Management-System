<?php include 'functions.php';

displayErrors();

$username = "XXXX";
$password = "XXXX";
$data = "username=".$username."&password=".$password;

$result = clearSID($data, $username);

$cinfo = json_decode($result, true);
echo "<pre>";
echo print_r($cinfo);
echo "</pre>";
?>
