<?php
include ('templates/upper.html');
include('db/connect_db.php');
?>
<form name=player>
	<?php
            $players = $db->prepare("SELECT * FROM player");
            $players->execute();
            $result = $players->fetchAll();
            foreach($result as $row) {
                print('<input type="checkbox" value' . $row['player_id'] . '>');
                print($row['player_number'] . ' ' . $row['last_name']);
                print('</input>');
            }
	?>
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
    <select name="match">
        <?php
            $match = $db->prepare("SELECT * FROM match");
            $match->execute();
	$opponent = $db->prepare("SELECT * FROM opponent");
	$opponent->execute();
	$result = $opponent->fetchAll();
	$arr = array();
	foreach($result as $row) {
		$arr[$row['opponent_id']] = $row['name'];
	}
	//var_dump($arr);
            $result = $match->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['match_id'] . '>');
                print($row['date'] . ' ' . $arr[$row['opponent_id']]);
                print('</option>');
            }
        ?>
    </select>
	<input type="submit"/>
</form>

<?php
include('db/close_db.php');
include('templates/lower.html');
?>
