
<?php 

$dbServerName = "localhost";
$dbuserName = "root";
$dbPassword = "";
$dbName = "kmn (pvt) ltd";

$connect = mysqli_connect($dbServerName,$dbuserName,$dbPassword,$dbName);

if(!$connect){

die("connection failed:".mysqli_connect_error());

}




?>