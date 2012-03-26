<?php
include 'templates/upper.html';
//$default_query="SELECT (player_number, last_name, (select count(*) FROM statistics_event WHERE item_id=1 AND player_id=1)) FROM player WHERE player_id=1";
include('db/connect_db.php');
$query = $db->prepare("SELECT player_number, last_name, first_name, (SELECT COUNT(*) FROM statistics_event WHERE item_id = :item_id AND player.player_id = statistics_event.player_id) AS stats FROM player ORDER BY player_number DESC");

$query->bindParam(':item_id', $item_id);
$query->bindParam(':player_number', $player_number);
$query->bindParam(':last_name', $last_name);
$query->bindParam(':first_name', $first_name);
$query->bindParam(':where', $where);
$query->bindParam(':order', $order);

$item_id = '1';
$player_number = '';
$last_name = '';
$first_name = '';
$where = '';
$order = 'player_number'; //jostain syystä kyselyyn ei tulostu oikeaa
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
<form>
    <select name="order">
        <?php
            $stat_items = $db->prepare("SELECT * FROM statistics_item");
            $stat_items.execute();
            $result = $stat_items.fetchAll();
            foreach($result as $row) {
                print("<option value=" . $row['item_id'] . ">");
                print($row['name']);
                print("</option>");
            }
        ?>
    </select>
</form>
<?php
include('db/close_db.php');
include 'templates/lower.html';
?>