<!--
File: upload_existing.php
Type: PHP (with some HTML)
Purpose: This file uses css bootstrap and js to create a webpage that will
allow users to upload an existing document to the DB.
-->
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Upload (Existing)</title>
<link href = "assets/css/bootstrap.css" rel = "stylesheet" /> <!-- bootstrap css -->
<script src = "assets/js/bootstrap.js"></script> <!-- bootstrap js -->
</head>
<body>
<?php
	// include functions and create link to db
	include "functions.php";
	include "db_functions.php";
	$dblink = db_connect("docstorage");

	// link to main page
	echo '<p><a class="btn btn-link btn-sm" href="upload_main.php">Back to Main Page</a></p>';
	
	echo '<div id = "page-inner">';
	
	// page headline
	echo '<h1 class="page-head-line">Database Management System - Upload (Existing)</h1>';
	echo '<p><em>Upload a file with a loan number already existing in the database.</em></p>';
	echo '<hr>';
	
	// dropdowns for the type of document (loan num + loan type)
	echo '<div class="panel-body">';
	// checking for errors
	if (isset($_REQUEST['err'])) {
		switch ($_REQUEST['err']) {
			case 'invalidLoanNum':
				echo '<div class="alert alert-danger alert-dismissable">';
				echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
				echo 'ERROR: Invalid loan number.';
				echo '</div>';
				break;
			case 'invalidFileType':
				echo '<div class="alert alert-danger alert-dismissable">';
				echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
				echo 'ERROR: Invalid file type.';
				echo '</div>';
				break;
		}
	}
	echo '<h3 class="page-inner">Select Loan Number & Document Type</h3>';
	echo '<form method="post" enctype="multipart/form-data" action="">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000">';
	// loan numbers
	echo '<div class="form-group">';
	echo '<label for="loanNum" class="control-label">Loan Number</label>';
	echo '<select class="form-control" name="loanNum">';
	$loan_num_sql = "SELECT `loan_id` from `loan_ids` WHERE 1";
	$result = $dblink->query($loan_num_sql) or
		die("Something went wrong with the sql: $loan_num_sql<br>".$dblink->errno);
	while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
		echo '<option value="'.$data['loan_id'].'">'.$data['loan_id'].'</option>';
	}
	echo '</select>';
	echo '</div>'; // end of form-group (loan numbers)
	// loan types
	echo '<div class="form-group">';
	echo '<label for="docType" class="control-label">Document Type</label>';
	echo '<select class="form-control" name="docType">';
	$doc_type_sql = "SELECT `loan_type` FROM `loan_types` WHERE 1";
	$result = $dblink->query($doc_type_sql) or
		die("Something went wrong with the sql: $doc_type_sql<br>".$dblink->errno);
	while($data = $result->fetch_array(MYSQLI_ASSOC)) {
		echo '<option value="'.$data['loan_type'].'">'.$data['loan_type'].'</option>';
	}
	echo '</select>';
	echo '</div>'; // end of form-group (doc type)
	
	// file upload functionality
	echo '<div class="form-group">';
	echo '<h3 class="page-inner">Choose File to Upload</h3>';
	echo '<label class="control-label">File Upload</label>';
	echo '<input name="userfile" type="file" accept=".pdf" />';
	echo '</div>'; // end of form-group (file upload)
	echo '<button type="submit" name="submit" value="submit" class="btn btn-block btn-success btn-lg">Upload File</button>';
	echo '</form>'; // end of form
	echo '</div>'; // end of panel-body div
	echo '</div>'; // end of page-inner div
	
	// php check for file
	if (isset($_POST['submit'])) {
		$dblink = db_connect("docstorage");
		$uploadDate = date('Ymd_H_i_s');
		$loanNum = $_POST['loanNum'];
		$docType = $_POST['docType'];
		$tmpName = $_FILES['userfile']['tmp_name']; // file name
		$fileSize = $_FILES['userfile']['size']; // file size
		$fileType = $_FILES['userfile']['type']; // file type (pdf)
		$test = strpos($fileType, 'pdf');
		if (strpos($fileType, 'pdf') == false) {
			echo '<div><h3>ERROR: invalid file type.</h3></div>';
			redirect("https://ec2-3-15-27-253.us-east-2.compute.amazonaws.com/upload_existing.php?msg=err-invalidFileType");
		}
		
		// reading the file
		$fp = fopen($tmpName, 'r');
		$tmp_content = fread($fp, filesize($tmpName));
		fclose($fp);
		$file_content = addslashes($tmp_content);
		
		// inserting into files server directory
		$fileName = "$loanNum-$docType-$uploadDate.pdf";
		$fp = fopen("/var/www/html/files/$fileName", "wb");
		fwrite($fp, $file_content);
		fclose($fp);
		
		// inserting into file_contents
		$scnd_sql = "INSERT INTO `file_contents` (`file_name`, `file_content`)
		VALUES ('$fileName', '$file_content')";
		$dblink->query($scnd_sql) or
			die ("<p>Something went wrong with $scnd_sql</p>".$dblink->errno);
		
		echo '<div><h3>File successfully uploaded!</h3></div>';
		redirect("https://ec2-3-15-27-253.us-east-2.compute.amazonaws.com/upload_main.php?msg=success");
	}
?>
</body>
</html>