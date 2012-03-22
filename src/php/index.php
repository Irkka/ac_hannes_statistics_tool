<?php
include 'templates/upper.html';
//$default_query="SELECT (player_number, last_name, (select count(*) FROM statistics_event WHERE item_id=1 AND player_id=1)) FROM player WHERE player_id=1";
include ("db/populate.php");

include 'templates/lower.html'
?>
