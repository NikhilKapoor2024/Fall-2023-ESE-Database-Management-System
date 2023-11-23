<?php
include "functions.php";
include "db_functions.php";
$dblink = db_connect('docstorage');
$fid = $_REQUEST['fid'];
$view_sql = "SELECT `file_content` FROM `files` WHERE `auto_id` = '$fid'";
$view_result = $dblink->query($view_sql);
$data = $view_result->fetch_array(MYSQLI_ASSOC);
header('Content-Type: application/pdf');
header('Content-Length: '.strlen($data['file_content']));
echo $data['file_content'];
?>