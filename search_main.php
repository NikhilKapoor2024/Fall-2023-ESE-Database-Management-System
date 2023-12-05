<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Search (Main)</title>
<link href = "assets/css/bootstrap.css" rel = "stylesheet" /> <!-- bootstrap css -->
<script src = "assets/js/bootstrap.js"></script> <!-- bootstrap js -->
</head>
<body>
<?php
	echo '<div id="page-inner">';
	echo '<h1 class="page-head-line">Database Management System - Search (Main Page)</h1>';
	echo '<p class="page-head-line"><em>Press any of the buttons below to search for a specific file in the database via that criteria.</em></p>';
	echo '<hr>';
	echo '<div class="panel-body">';
	if (isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "success")) {
		echo '<div class="alert alert-success alert-dismissable">';
		echo '<button type="button" class="close" data-dismiss="alet" aria-hidden="true">x</button>';
		echo 'Your document has been successfully updated';
		echo '</div>'; // end of success
	}
	echo '<p><a class="btn btn-info btn-block" href="search_loannum.php">Search by Loan Number</a></p>';
	echo '<p><a class="btn btn-info btn-block" href="search_loantype.php">Search by Loan Type</a></p>';
	echo '<p><a class="btn btn-info btn-block" href="search_filetype.php">Search by File Type</a></p>';
	echo '<p><a class="btn btn-info btn-block" href="search_datecreated.php">Search by Date Created</a></p>';
	echo '<p><a class="btn btn-info btn-block" href="search_all.php">List all Files</a></p>';
	echo '</div>'; // end of panel-body
	echo '</div>'; // end of page-inner
?>
</body>
</html>
