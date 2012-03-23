<?php
include 'templates/upper.html';
//$default_query="SELECT (player_number, last_name, (select count(*) FROM statistics_event WHERE item_id=1 AND player_id=1)) FROM player WHERE player_id=1";
include('db/connect_db.php');
$query = $db->prepare("SELECT player_number, last_name, first_name, (SELECT COUNT(*) FROM statistics_event WHERE item_id = :item_id AND player.player_id = statistics_event.player_id) FROM player ORDER BY player_number");
$query->bindParam(':item_id', $item_id);
$query->bindParam(':player_number', $player_number);
$query->bindParam(':last_name', $last_name);
$query->bindParam(':first_name', $first_name);
$query->bindParam(':where', $where);

//Näihin voi laittaa kyselyt WHERE item_id on dropboxissa olevan itemin nimi
$item_id = 1;
$player_number = '';
$last_name = '';
$first_name = '';
$where = '';
?>
<table>
<?php
//var_dump($query);
$query->execute();
$result = $query->fetchAll();
//var_dump($result);

foreach($result as $row) {
    print('<tr>');
    print('<td>' . $row['player_number'] . '</td>');
    print('<td>' . $row['last_name'] . '</td>');
    print('<td>' . $row['first_name'] . '</td>');
    print('<td>' . $row[3] . '</td>');
    print('</tr>');
}

?>
</table>
<?php
include('db/close_db.php');
include 'templates/lower.html';
?>