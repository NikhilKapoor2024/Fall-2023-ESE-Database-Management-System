<!--
File: upload_page.php
Type: PHP (with some HTML)
Purpose: This file uses css bootstrap and js to create a webpage that will
allow users to upload either a new document or an existing document to the
DB.
-->
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Upload (Main)</title>
<link href = "assets/css/bootstrap.css" rel = "stylesheet" /> <!-- bootstrap css -->
<script src = "assets/js/bootstrap.js"></script> <!-- bootstrap js -->
</head>
<body>
<?php
	echo '<div id="page-inner">';
	echo '<h1 class="page-head-line">Database Management System - Upload (Main Page)</h1>';
	echo '<p class="page-head-line"><em>Press Upload New Loan to upload a file with a new loan or press Upload Existing Loan to upload a file with a previous loan</em></p>';
	echo '<hr>';
	echo '<div class="panel-body">';
	if (isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "success")) {
		echo '<div class="alert alert-success alert-dismissable">';
		echo '<button type="button" class="close" data-dismiss="alet" aria-hidden="true">x</button>';
		echo 'Your document has been successfully updated';
		echo '</div>'; // end of success
	}
	echo '<p><a class="btn btn-primary" href="upload_new.php">Upload New Loan</a></p>';
	echo '<p><a class="btn btn-primary" href="upload_existing.php">Upload Existing Loan</a></p>';
	echo '</div>'; // end of panel-body
	echo '</div>'; // end of page-inner
?>
</body>
</html>