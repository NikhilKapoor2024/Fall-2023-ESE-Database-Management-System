<?php

// db_connect($db): function to connect to the databse on phpmyadmin
function db_connect($db) {
	$hostname = "localhost";
	$username = "webuser";
	$password = "A_H8M@Feq[WvM2y8";
	
	$dblink = new mysqli($hostname, $username, $password, $db);
	if (mysqli_connect_errno()) {
		die("Error connecting to database: ".mysqli_connect_errno());
	}
	
	return $dblink;
}

// logSessionOpened($sid, $timestamp): creates a sql command to send info about a created session into
// the session_log table in the db
function logSessionOpened($sid, $timestamp) {
	$sql = "INSERT into `session_log`
	(`session_id`, `opened_at`, `closed_at`, `status`) VALUES ('$sid', '$timestamp', 'TBD', 'open')";
	
	return $sql;
}

// updateSessionStatus($sid, $closed_at, ): function to update a previously logged session's status
function logSessionClosed($sid, $timestamp) {
	$sql = "UPDATE `session_log` SET `status` = 'closed', `closed_at` = '$timestamp' WHERE `session_id` = '$sid'";
	
	return $sql;
}

// fileToDB(): creates the sql line to send the file to the database
function sqlFileToDB($result, $dblink, $currentFile, $fileData) {
	
	$tmp1 = explode("-", $currentFile);
	$tmp2 = explode(".", $tmp1[2]);
	
	$account_num = $tmp1[0];
	$loan_type = $tmp1[1];
	$date_created = $tmp2[0];
	$file_type = $tmp2[1];
	
	// send the contents of the file to the file_contents table
	$file_contents_sql = "INSERT into `file_contents` (`file_name`, `content`)
		VALUES ('$currentFile', '$fileData')";
		$dblink->query($file_contents_sql) or
			die("<h3>Error with the following sql: $file_contents_sql".$dblink->errno);
	
	// send the loan id to the loan_ids table
	$check_sql = "SELECT `loan_id` FROM `loan_ids` WHERE `loan_id` = '$account_num'";
	$result = $dblink->query($check_sql) or
		die("<h3>Error with the following sql: $check_sql".$dblink->errno);
	if ($result->num_rows <= 0) {
		$pass_sql = "INSERT INTO `loan_ids` (`loan_id`) VALUES ('$account_num')";
		$dblink->query($pass_sql) or
			die("<h3>Error with the following sql: $pass_sql".$dblink->errno);
	}
	
	// return sql command
	$sql = "INSERT into `files`
	(`account_num`, `loan_type`, `date_created`, `file_type`, `file_content`, `file_status`)
	VALUES ('$account_num', '$loan_type', '$date_created', '$file_type', '$fileData', 'active')";
	
	return $sql;
}

// sendPDFstoFile($currentFile): function to send the pdf files to the files directory
function check_file($currentFile) {
	// regex for full file name
	$regex = "/[0-9]+-[A-Za-z]+-[0-9]+_[0-9]+_[0-9]+_[0-9]+\\.[A-Za-z]+/i";
	if (preg_match($regex, $currentFile) == 1) {
		return true;
	}
	else {
		return false;
	}
}

// sendFilesToDB($sid, $dblink): function to verify that the files are true and to
// send them from the files directory in the server to the files table in the db
function sendFilesToDB($sid, $dblink, $currentFile, $cqr_result) {
	$dir = new DirectoryIterator(dirname("/var/www/html/files/*.pdf"));
	
	// traverse through file directory until the right file is found
	foreach($dir as $file_info) {
		if (!$file_info->isDot()) {
			$file_name = $file_info->getFilename();
			if ($file_name != $currentFile) {
				continue;
			}
			
			echo "<div><h3>Opening file...</h3></div>";
			$path = "/var/www/html/files/$file_name";
			$fp = fopen($path, "r");
			if ($fp) {
				echo "<div><h3>file is open!</h3></div>";
			}
			else {
				echo "<div><h3>error opening file</h3></div>";
			}
		
			// if the file path is empty log it into file_errors table and move on to the next file
			if (filesize($path) == 0) {
				$timestamp = date("Y-m-d H:i:s");
				$sql = "INSERT into `file_errors` (`session_id`, `file_name`, `error_msg`) VALUES ('$sid', '$path', 'empty file')";
				$dblink->query($sql) or
					die("<h3>Something went wrong with the following sql: $sql<br>".$dblink->errno);
			
				continue;
			}
		
			// once you find a valid file, close the directory and send it to the files table
			$fileData = fread($fp, filesize("/var/www/html/files/".$file_info));
			fclose($fp);
			$fileDataClean = addslashes($fileData);
			
			$insert_sql = sqlFileToDB($cqr_result, $dblink, $currentFile, $fileDataClean);
			$dblink->query($insert_sql) or
				die("<h3>Something went wrong with the following sql command: $sql<br>".$dblink->errno);
		
			echo "<div><h3>File ".$currentFile." successfully written to the system.</h3></div>";
		}
	}
}

// redirect($uri): redirect user to a page using php and javascript
function redirect($uri) {
	?>
		<script type="text/javascript">
			<!---
				document.location.href = "<?php echo $uri ?>";
			--->
		</script>
	<?php die;
}
?>