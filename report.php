<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>D.M.S - Final Report</title>
<link href = "assets/css/bootstrap.css" rel = "stylesheet" /> <!-- bootstrap css -->
<script src = "assets/js/bootstrap.js"></script> <!-- bootstrap js -->
</head>
<body>
<?php
	include 'functions.php';
	include 'db_functions.php';
	$dblink = db_connect('docstorage');
	
	echo '<div id="page-inner">';
	echo '<h1 class="page-head-line">Database Management System - Final Report</h1>';
	echo '<p class="page-head-line"><em>This is the final report on the contents of the database from 11/3/2023 12AM to 11/19/2023 11:59 PM</em></p>';
	echo '<hr>';
	echo '<div class="panel-body">';
	
	// Report 1
	echo '<h2><u>Report 1: Loan Numbers</u></h2>';
	$r1_sql = "SELECT DISTINCT `account_num` FROM `files` WHERE `date_created` BETWEEN '20231103%' AND '20231119%'";
	$r1_result = $dblink->query($r1_sql) or
		die ('<p>Something went wrong with the following sql:'.$r1_result.'</p>'.$dblink->error);
	echo '<h3>Total # of loan numbers: '.$r1_result->num_rows.'</h3>';
	while ($data = $r1_result->fetch_array(MYSQLI_ASSOC)) {
		echo '<p>Loan number: <b>'.$data['account_num'].'</b></p>';
	}
	
	// Report 2
	echo '<h2><u>Report 2: Documents (Size)</u></h2>';
	$arrayOfFileSizes = array();
	$numLoanNumbers = array();
	$r2_sql = "SELECT `account_num`, `file_size` FROM `files` WHERE `date_created` BETWEEN '20231103%' AND '20231119%'";
	$r2_result = $dblink->query($r2_sql) or
		die("<p>Something went wrong with the following sql: $r2_sql</p>".$dblink->error);
	while ($data = $r2_result->fetch_array(MYSQLI_ASSOC)) {
		$totalFileSize += $data['file_size'];
		$arrayOfFileSizes[] = $data['file_size'];
		$loanNums[] = $data['account_num'];
	}
	echo '<h3>Total file size: '.$totalFileSize.' Bytes --- ';
	$avgTotalFileSize = ceil($totalFileSize / count($loanNums));
	echo 'Average file size: '.$avgTotalFileSize.' Bytes</h3>';
	
	// Report 3
	echo '<h2><u>Report 3: Documents (Number of)</u></h2>';
	$r3_sql = "SELECT `account_num` FROM `files` WHERE `date_created` BETWEEN '20231103%' AND '20231119%'";
	$r3_result = $dblink->query($r3_sql) or die("<p>Error with: $r3_sql</p>".$dblink->error);
	$loanArray = array();
	while ($data = $r3_result->fetch_array(MYSQLI_ASSOC)) {
		$loanArray[] = $data['account_num'];
	}
	$loanUnique = array_unique($loanArray);
	$averageNumFiles = intdiv(count($loanArray), count($loanUnique));
	echo '<h3>Total documents recieved: '.count($loanArray);
	echo ' --- Average of documents recieved: '.$averageNumFiles.' (from '.count($loanUnique).' loan numbers)</h3>';
	
	// Report 4
	echo '<h2><u>Report 4: Documents (Per Loan Number)</h2></u>';
	foreach($loanUnique as $key=>$value) {
		$sql = "SELECT `file_size`, `file_status` FROM `files` WHERE
		(`date_created` BETWEEN '20231103%' AND '20231119%')
		AND (`account_num` LIKE '$value')";
		$res = $dblink->query($sql) or die("<p>Error with: $sql</p>".$dblink->error);
		while ($data = $res->fetch_array(MYSQLI_ASSOC)) {
			$numFilesPerLoan = $res->num_rows;
			$totalFileSizePerLoan += $data['file_size'];
		}
		$avgSizePerLoan = intdiv($totalFileSizePerLoan, $numFilesPerLoan);
		echo '<p>Number of files from <b>'.$value.'</b>: '.$numFilesPerLoan.' Files, ';
		if ($numFilesPerLoan > $averageNumFiles) {
			echo '<b>above average.</b></p>';
		}
		elseif($numFilesPerLoan < $averageNumFiles) {
			echo '<b>below average.</b></p>';
		}
		echo '<p>&emsp;Average file size for <b>'.$value.'</b>: '.$avgSizePerLoan.' Bytes, ';
		if ($avgSizePerLoan > $avgTotalFileSize) {
			echo ' <b>above average</b></p>';
		}
		elseif ($avgSizePerLoan < $avgTotalFileSize){
			echo ' <b>below average</b></p>';
		}
	}
	
	// Report 5
	//credit, closing, title, financial, personal, internal, legal, other
	echo '<h2><u>Report 5: Loan Numbers (Loan Types)</u></h2>';
	$r5_sql = "SELECT account_num,
	COUNT(*) as total,
	SUM(CASE WHEN loan_type LIKE 'Credit' THEN 1 ELSE 0 END) AS numCredit,
	SUM(CASE WHEN loan_type LIKE 'Closing' THEN 1 ELSE 0 END) AS numClosing,
	SUM(CASE WHEN loan_type LIKE 'Title' THEN 1 ELSE 0 END) AS numTitle,
	SUM(CASE WHEN loan_type LIKE 'Financial' THEN 1 ELSE 0 END) AS numFinancial,
	SUM(CASE WHEN loan_type LIKE 'Personal' THEN 1 ELSE 0 END) AS numPersonal,
	SUM(CASE WHEN loan_type LIKE 'Internal' THEN 1 ELSE 0 END) AS numInternal,
	SUM(CASE WHEN loan_type LIKE 'Legal' THEN 1 ELSE 0 END) AS numLegal,
	SUM(CASE WHEN loan_type LIKE 'Other' THEN 1 ELSE 0 END) AS numOther
	FROM files
	WHERE (`date_created` BETWEEN '20231103%' AND '20231119%')
	GROUP BY account_num";
	$r5_result = $dblink->query($r5_sql) or die("<p>Error with: $r5_sql</p>".$dblink->error);
	$loanTypesArray = array();
	$numCompleteLoans = 0;
	while ($data = $r5_result->fetch_array(MYSQLI_ASSOC)) {
		$docsMissingCount = 0;
		echo '<p>Loan number '.$data['account_num'].':</p>';
		foreach($data as $key=>$value) {
			if ($key !== 'account_num' && $key !== 'total' && $value != 0) {
				$loanTypesArray[$key] += $value;
			}
			if ($value == 0) {
				$docsMissingCount++;
				$loan_type = preg_split('/(?=[A-Z])/', strval($key));
				echo '<p>&emsp;is missing the document type <b><u>'.$loan_type[1].'</b></u></p>';
			}
			
		}
		if ($docsMissingCount == 0) {
			$numCompleteLoans++;
			echo '<p>&emsp;<b>is missing no document type</b></p>';
		}
		echo '<br>';
	}
	
	// Report 6
	echo '<h2><u>Report 6: Total # of Documents Types Recieved Across All Loan Numbers</u></h2>';
	foreach ($loanTypesArray as $key=>$value) {
		$loan_type = preg_split('/(?=[A-Z])/', strval($key));
		echo '<h3>Total amount of '.$loan_type[1].' documents: <b><u>'.$value.'</u></b></h3>';
	}
	
	// Notes
	echo '<h2><u>NOTES:</u></h2>';
	echo '<p> -> Although this data represents all files taken from November 3rd to November 19th, there were many other files that were recieved by the system past the end date. I elected to only use the data within the timeframe as described in the description of the assignment.</p>';
	echo '<p> -> All cronjobs stopped at around Thursday, November 30th, at 11:15pm.</p>';
	
	// end of page
	echo '</div>'; // end of panel-body
	echo '</div>'; // end of page-inner
?>
</body>
</html>