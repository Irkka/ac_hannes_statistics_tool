<?php
include('config.php');
try {
$db=new PDO('pgsql:host=' . $hostname . ';dbname=' . $db_name . ';', $user, $pass);
}

catch(PDOException $e) {
	print "Error: " . $e->getMessage() . "<br/>";
	die();
}
?>
