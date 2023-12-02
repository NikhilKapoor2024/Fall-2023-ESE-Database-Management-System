<?php

// displayErrors(): displays the errors of the page
function displayErrors() {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
// createSession($data): creates the session and returns the result of the cURL execution
function createSession($data) {
	$ch = curl_init('.../api/create_session');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data))
	);

	// timing the amount of time taken to create a session
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start)/60;
	curl_close($ch);
	echo "<h3>createSession Execution time: $execution_time</h3>";

	return $result;
}

// closeSession($cinfo): closes the session by getting the sid
function closeSession($data) {
	$ch = curl_init('.../api/close_session');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data))
	);
	
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start)/60;
	curl_close($ch);
	echo "<h3>closeSession Execution time: $execution_time</h3>";
	
	return $result;
}

//clearSID(): clears the session with the SID
function clearSID($data) {
	$ch = curl_init('.../api/clear_session');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data))
	);
	
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start)/60;
	curl_close($ch);
	echo "<h3>clearSID Execution time: $execution_time</h3>";
	
	return $result;
}

//createQuery($cinfo, $data): creates the query from the cinfo array
// NOTE: Will have to flesh out logic later, this is just a placeholder
function createQuery($cinfo, $username) {
	// NOTE: Try and make 55-70 it's own function
	if ($cinfo[0] == "Status: OK" && $cinfo[1] == "MSG: Session Created") {
		$data = "uid=".$username."&sid=".$cinfo[2];
		$ch = curl_init('.../api/query_files');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'content-type: application/x-www-form-urlencoded',
			'content-length: '.strlen($data))
		);

		// timing the amount of time taken to create a session
		$time_start = microtime(true);
		$result = curl_exec($ch);
		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start)/60;
		curl_close($ch);
		echo "<h3>createQuery Execution time: $execution_time</h3>";
		
		return $result;
	}
	else {
		echo "<pre>";
		echo print_r($cinfo);
		echo "</pre>";
	}
}

// createQueryRequest($data): This function calls the request_file API to get the actual file
// and store them into a db
function createQueryRequest($data) {
	$ch = curl_init('.../api/request_file');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data))
	);
	
	// execution time
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start)/60;
	curl_close($ch);
	echo "<h3>createQueryRequest Execution time: $execution_time</h3>";
	
	return $result;
}

?>
