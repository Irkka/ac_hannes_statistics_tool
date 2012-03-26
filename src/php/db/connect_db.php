<?php
try {
$user='ilkkahak';
$pass='mestaruus'; //supply password here
$db=new PDO('pgsql:host=localhost;dbname=ilkkahak;', $user, $pass);
/*	foreach($db->query('SELECT * FROM player') as $row) {
		print_r($row);
	}

	foreach($db->query('SELECT * FROM match') as $row) {
		print_r($row);
	}

	foreach($db->query('SELECT * FROM opponent') as $row) {
		print_r($row);
	}
		foreach($db->query("SELECT * FROM statistics_event WHERE player_id=(SELECT player_id from player WHERE player_number=7) AND item_id=(SELECT item_id FROM statistics_item WHERE name='maali')") as $row) {
		print_r($row);
	} */
}

catch(PDOException $e) {
	print "Error: " . $e->getMessage() . "<br/>";
	die();
}
?>
