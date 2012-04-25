<?php
try {
$user='ilkkahak';
$pass='mestaruus'; //supply password here
$db=new PDO('pgsql:host=localhost;dbname=ilkkahak;', $user, $pass);
}

catch(PDOException $e) {
	print "Error: " . $e->getMessage() . "<br/>";
	die();
}
?>
