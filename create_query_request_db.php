<?php
include 'functions.php';
include 'db_functions.php';

displayErrors();

// connecting to the database
$dblink = db_connect("docstorage");

// creating a session and logging the time it was created
$username = "XXXX";
$uid = $username;
$password = "XXXX";
$data = "username=".$username."&password=".$password;
$result = createSession($data);
$cinfo = json_decode($result, true);
if ($cinfo[0] == "Status: OK" && $cinfo[1] == "MSG: Session Created") {
	$sid = $cinfo[2];
	$opened_at = date("Y-m-d H:i:s");
	$open_sql = logSessionOpened($sid, $opened_at);
	$dblink->query($open_sql) or
		die("<h3>Something went wrong with the following sql command: $open_sql<br>".$dblink->errno);
	
}
else {
	$clear_result = clearSID($data);
	$cinfo = json_decode($clear_result, true);
	echo "<pre>";
	echo print_r($cinfo);
	echo "</pre>";
}
$cq_result = createQuery($cinfo, $username);
$tmp = json_decode($cq_result, true);
$tmp2 = explode(":", $tmp[1]);
$files = json_decode($tmp2[1], true);

// deliminating each file and storing them into readable format
foreach($files as $key=>$value) {
	$tmp = explode("/", $value);
	$currentFile = $tmp[4];
	echo "<div>About to process: ".$currentFile."</div>";
	if (check_file($currentFile) == false) {
		$timestamp = date("Y-m-d H:i:s");
		$error_sql = "INSERT into `file_errors` (`session_id`, `file_name`, `error_msg`, `date_inserted`) VALUES ('$sid', '$currentFile', 'regex error', '$timestamp')";
		$dblink->query($error_sql) or 
			die("<h3>Something went wrong with the following sql command: $error_sql<br>".$dblink->errno);
		echo "<div><h4>Error with $currentFile: logged into file_errors</h4></div>";
	}
	else {
		$data = "sid=".$sid."&uid=".$uid."&fid=".$currentFile;
		$cqr_result = createQueryRequest($data);
	
		// sending the files to the files directory
		$fp = fopen("/var/www/html/files/$currentFile", "wb");
		fwrite($fp, $cqr_result);
		fclose($fp);
		
		//sending the files to the DB:
		sendFilesToDB($sid, $dblink, $currentFile, $cqr_result);
	}
}

$cs_data = "sid=".$sid."&uid=".$uid;
$cs_result = closeSession($cs_data);
$cs_cinfo = json_decode($cs_result, true);

if ($cs_cinfo[0] == "Status: OK") {
	$closed_at = date("Y-m-d H:i:s");
	$close_sql = logSessionClosed($sid, $closed_at);
	$dblink->query($close_sql) or
		die("<h3> Something went wrong with the following sql command: $close_sql<br>".$dblink->errno);
	
	echo "Session successfully closed!\r\n";
	echo "Session ID: ".$sid."\r\n";
}
else {
	echo "<pre>";
	echo print_r($cinfo);
	echo "</pre>";
}

?>
