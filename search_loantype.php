<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Search (Loan Type)</title>
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
	echo '<p><a class="btn btn-link btn-sm" href="search_main.php">Back to Main Page</a></p>';
	
	// input and submit button
	echo '<div id="page-inner">';
	echo '<h1 class="page-head-line">Database Management System - Search (Loan Type)</h1>';
	echo '<p class="page-head-line"><em>Follow the instructions below to search for the files in the databse via their loan type.</em></p>';
	echo '<hr>';
	echo '<div class="panel-body">';
	if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == 'noResult') {
		echo '<div>';
		echo '<button class="btn btn-lg btn-danger" disabled="disabled">No result</button>';
	}
	echo '<form method="post" action="">';
	echo '<div class="form-group">';
	echo '<label for="loanType" class="control-label">Loan Type</label>';
	echo '<input type="text" class="form-control" name="loanType">';
	echo '</div>'; // end of form-group (loan numbers)
	echo '<button name="submit" type="submit" class="btn btn-success btn-lg" value="submit">Search</button>';
	echo '</form>';
	echo '</div>'; // end of panel-body
	echo '</div>'; // end of page-inner
	
	// display results
	if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
		$loanType = $_POST['loanType'];
		$dblink = db_connect('docstorage');
		$match_sql = "SELECT * FROM `loan_types` WHERE `loan_type` = '$loanType'";
		$result = $dblink->query($match_sql);
		if ($result->num_rows <= 0) {
			redirect('https://ec2-3-15-27-253.us-east-2.compute.amazonaws.com/search_loantype.php?msg=noResult');
		}
		else { // showcase table of the files located
			// get the files
			$fetch_sql = "SELECT * FROM `files` WHERE `loan_type` LIKE '$loanType%'";
			$fetch_result = $dblink->query($fetch_sql);
			echo '<h2>Matches for '.$loanType.': '.$fetch_result->num_rows.'</h2>';
			echo '<table class="table table-hover table-bordered">';
			
			// header
			echo '<tr><th>Loan #</th><th>Loan Type</th><th>Date Created</th><th>File Type</th><th>View File</th></tr>';
			while ($data = $fetch_result->fetch_array(MYSQLI_ASSOC)) {
				echo '<tr>';
				echo '<td>'.$data['account_num'].'</td>';
				echo '<td>'.$data['loan_type'].'</td>';
				echo '<td>'.$data['date_created'].'</td>';
				echo '<td>'.$data['file_type'].'</td>';
				echo '<td><a href="https://ec2-3-15-27-253.us-east-2.compute.amazonaws.com/view_file.php?fid='.$data['auto_id'].'" target="_blank">View File</td>';
				echo '</tr>';
			}
			
			echo '</table>'; // end of table
		}
	}
?>
</body>
</html>