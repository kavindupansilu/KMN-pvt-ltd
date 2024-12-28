
<?php 

$dbServerName = "localhost";
$dbuserName = "root";
$dbPassword = "";
$dbName = "kmn (pvt) ltd";

$conn = mysqli_connect($dbServerName,$dbuserName,$dbPassword,$dbName);

if(!$conn){

die("connection failed:".mysqli_connect_error());

}




?>