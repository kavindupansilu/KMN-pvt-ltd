<?php

require "dbh.inc.php";
if (!$connect) {
  die("Connection failed: " . mysqli_connect_error());
}
?>

<html>
<body>
<h1>Booking Added Successfully</h1>
</body>
</html>
