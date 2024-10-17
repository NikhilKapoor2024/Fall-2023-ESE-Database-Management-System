<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Search (Date Created)</title>
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
	echo '<h1 class="page-head-line">Database Management System - Search (Date Created)</h1>';
	echo '<p class="page-head-line"><em>Follow the instructions below to search for the files in the databse via the date they were created.</em></p>';
	echo '<p>NOTE: This only is for the <em>year, month, and day</em> the file was created, not the specific time.</p>';
	echo '<hr>';
	echo '<div class="panel-body">';
	if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == 'noResult') {
		echo '<div>';
		echo '<button class="btn btn-lg btn-danger" disabled="disabled">No result</button>';
	}
	echo '<form method="post" action="">';
	echo '<div class="form-group">';
	echo '<label for="yearCreated" class="control-label">Year</label>';
	echo '<input type="text" class="form-control" name="yearCreated">';
	echo '<label for="monthCreated" class="control-label">Month</label>';
	echo '<select class="form-control" name="monthCreated">';
	$year_created_sql = "SELECT `month_name` FROM `months` WHERE 1";
	$result = $dblink->query($year_created_sql);
	while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
		echo '<option value="'.$data['month_name'].'">'.$data['month_name'].'</option>';
	}
	echo '</select>';
	echo '<label for="dayCreated" class="control-label">Day</label>';
	echo '<input type="text" class="form-control" name="dayCreated">';
	echo '</div>'; // end of form-group (loan numbers)
	echo '<button name="submit" type="submit" class="btn btn-success btn-lg" value="submit">Search</button>';
	echo '</form>';
	echo '</div>'; // end of panel-body
	echo '</div>'; // end of page-inner
	
	// display results
	if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
		$dblink = db_connect('docstorage');
		$yearCreated = $_POST['yearCreated'];
		echo $yearCreated;
		$tmpMonth = $_POST['monthCreated'];
		echo $tmpMonth;
		$month_num_sql = "SELECT * FROM `months` WHERE `month_name` = '$tmpMonth'";
		$month_num_result = $dblink->query($month_num_sql);
		echo $month_num_result->num_rows;
		$dayCreated = $_POST['dayCreated'];
		echo $dayCreated;
		if ($month_num_result->num_rows <= 0) {
			die("<p>Error with the following sql: $month_num_sql".$dblink->error);
		}
		else {
			$data = $month_num_result->fetch_array(MYSQLI_ASSOC);
			$monthCreated = $data['auto_id'];
			echo $monthCreated;
		}
		$match_sql = "SELECT `auto_id` FROM `months` WHERE `auto_id` = '$monthCreated'";
		$result = $dblink->query($match_sql);
		if ($result->num_rows <= 0) {
			redirect('XXXX/search_loantype.php?msg=noResult');
		}
		else { // showcase table of the files located
			// get the files
			$dateCreated = $yearCreated.$monthCreated.$dayCreated;
			echo $dateCreated;
			$fetch_sql = "SELECT * FROM `files` WHERE `date_created` LIKE '$dateCreated%'";
			$fetch_result = $dblink->query($fetch_sql);
			echo '<h2>Matches for '.$dateCreated.': '.$fetch_result->num_rows.'</h2>';
			echo '<table class="table table-hover table-bordered">';
			
			// header
			echo '<tr><th>Loan #</th><th>Loan Type</th><th>Date Created</th><th>File Type</th><th>View File</th></tr>';
			while ($data = $fetch_result->fetch_array(MYSQLI_ASSOC)) {
				echo '<tr>';
				echo '<td>'.$data['account_num'].'</td>';
				echo '<td>'.$data['loan_type'].'</td>';
				echo '<td>'.$data['date_created'].'</td>';
				echo '<td>'.$data['file_type'].'</td>';
				echo '<td><a href="XXXX/view_file.php?fid='.$data['auto_id'].'" target="_blank">View File</td>';
				echo '</tr>';
			}
			
			echo '</table>'; // end of table
		}
	}
?>
</body>
</html>
