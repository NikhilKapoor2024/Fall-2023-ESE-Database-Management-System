<?php include 'functions.php';

displayErrors();

$username = "XXXX";
$password = "XXXX";
$data = "username=".$username."&password=".$password;

// initializing a session from a cURL
$result = createSession($data);
$cinfo = json_decode($result, true);
$sid = $cinfo[2];
$cq_result = createQuery($cinfo, $username);

// see what the data looks like
$tmp = json_decode($cq_result, true);
$files = explode(":", $tmp[1]);
echo "<pre>";
echo print_r(json_decode($files[1]));
echo "</pre>";

// closing session
$cs_data = "sid=".$sid."&uid=".$username;
$cs_result = closeSession($cs_data);
$cs_cinfo = json_decode($cs_result, true);

if ($cs_cinfo[0] == "Status: OK") {
	echo "Session successfully closed!\r\n";
	echo "Session ID: ".$sid."\r\n";
}
else {
	echo "<pre>";
	echo print_r($cinfo);
	echo "</pre>";
}
?>
