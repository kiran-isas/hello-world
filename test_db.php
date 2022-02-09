<?php
$host = "localhost";
$dbuid = "isasadmin";
$dbpwd = "isasadmin007";
$dbname = "isasbeautyschool_org";

//=======================================================

$host1 = "localhost";
$dbuid1	= "isasllp";
$dbpwd1 = "isasllp!@!2021";
$dbname1 = "isas.llp";

// PHP program to connect multiple MySQL database
// into single webpage
 
// Connection of first database
// Database name => database1
// Default username of localhost => root
// Default password of localhost is '' (none)

$link1 = mysql_connect($host ,$dbuid, $dbpwd);
mysql_select_db($dbname,$link1);
// Check for connection
if($link1 == true) {
    echo "database1 Connected Successfully";
}
else {
    die("ERROR: Could not connect " . mysql_error());
}
 
echo "<br>";

$selec_db1="select name,username, pass from site_setting where admin_id='69'";
$res = mysql_query($selec_db1,$link1);
 
$row = mysql_fetch_array($res);
echo "1 -".$row['username'] .' - '. $row['pass']."<br>";
// Connection of first database
// Database name => database1
//===========================================================
$link2 = mysql_connect($host1 ,$dbuid1, $dbpwd1);
mysql_select_db($dbname1,$link2);
 
// Check for connection
if($link2 == true) {
    echo "<br/>database2 Connected Successfully";
}
else {
    die("ERROR: Could not connect " . mysql_error());
}
// Connection of databases
//$link = mysqli_connect('localhost', 'root', '');
$selec_db2="select name,username, pass from site_setting where admin_id='1'";
$res2 = mysql_query($selec_db2,$link2);
 
$row2 = mysql_fetch_array($res2);
echo "<br/>2 -".$row2['username'] .' - '. $row2['pass']."<br>";
?>


