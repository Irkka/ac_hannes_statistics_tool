<?php
include 'templates/upper.html';
include('db/connect_db.php');
$query = $db->prepare("SELECT player_number, last_name, first_name, (SELECT COUNT(*) FROM statistics_event WHERE item_id = :item_id AND player.player_id = statistics_event.player_id) AS stats FROM player ORDER BY stats DESC");

$query->bindParam(':item_id', $item_id);
$query->bindParam(':player_number', $player_number);
$query->bindParam(':last_name', $last_name);
$query->bindParam(':first_name', $first_name);
$query->bindParam(':where', $where);
$query->bindParam(':order', $order);

if(empty($_POST))
	$item_id = '1';
else
	$item_id = $_POST['item'];
$player_number = '';
$last_name = '';
$first_name = '';
$where = '';
$order = 'stats';
//Näihin voi laittaa kyselyt WHERE item_id on dropboxissa olevan itemin nimi
?>
<table>
<?php
var_dump($query);
$query->execute();
$result = $query->fetchAll();
//var_dump($result);

foreach($result as $row) {
    print('<tr>');
    print('<td>' . $row['player_number'] . '</td>');
    print('<td>' . $row['last_name'] . '</td>');
    print('<td>' . $row['first_name'] . '</td>');
    print('<td>' . $row['stats'] . '</td>');
    print('</tr>');
}

?>
</table>
<form action="index.php" method="post">
	<select name="player">
		<option value="default_player"> Kaikki </option>
		<?php
            $players = $db->prepare("SELECT * FROM player");
            $players->execute();
            $result = $players->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['player_id'] . '>');
                print($row['first_name'] . ' ' . $row['last_name']);
                print('</option>');
            }
		?>
	</select>
    <select name="item">
		<option value="1"> Kaikki </option>
        <?php
            $stat_items = $db->prepare("SELECT * FROM statistics_item");
            $stat_items->execute();
            $result = $stat_items->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['item_id'] . '>');
                print($row['name']);
                print('</option>');
            }
        ?>
    </select>
    <select name="opponent">
		<option value="default_opponent"> Kaikki </option>
        <?php
            $opponent = $db->prepare("SELECT * FROM opponent");
            $opponent->execute();
            $result = $opponent->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['opponent_id'] . '>');
                print($row['name']);
                print('</option>');
            }
        ?>
    </select>
    <select name="field">
		<option value="default_field"> Kaikki </option>
        <?php
            $field = $db->prepare("SELECT * FROM field");
            $field->execute();
            $result = $field->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['field_id'] . '>');
                print($row['name']);
                print('</option>');
            }
        ?>
    </select>
<input type="submit" />
</form>
<a href="./add_event.php">add</a>
<?php
include('db/close_db.php');
include 'templates/lower.html';
?>
