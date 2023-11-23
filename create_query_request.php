<?php include 'functions.php';

displayErrors();

// creating a session
$username = "yct482";
$uid = $username;
$password = "kb3r7XBL4G9F6pdh";
$data = "username=".$username."&password=".$password;
$result = createSession($data);

// calling the createQuery function to get the data
$cinfo = json_decode($result, true);
$sid = $cinfo[2];
$cq_result = createQuery($cinfo, $username);
$tmp = json_decode($cq_result, true);
$tmp2 = explode(":", $tmp[1]);
$files = json_decode($tmp2[1], true);

// deliminating each file and storing them into readable format
foreach($files as $key=>$value) {
	$tmp = explode("/", $value);
	$currentFile = $tmp[4];
	echo "<div>About to process: ".$currentFile."</div>";
	$data = "sid=".$sid."&uid=".$uid."&fid=".$currentFile;
	$cqr_result = createQueryRequest($data);
	
//	$fp = fopen("/var/www/html/files/$currentFile", "wb");
//	fwrite($fp, $cqr_result);
//	fclose($fp);
	echo "<div><h3>File ".$currentFile." successfully written to the system.</h3></div>";
}

$cs_data = "sid=".$sid."&uid=".$uid;
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