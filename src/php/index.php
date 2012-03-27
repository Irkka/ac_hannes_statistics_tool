<?php
include 'templates/upper.html';
include('db/connect_db.php');

if(empty($_POST))
	$item_id = '1';
else
	$item_id = $_POST['item'];
//Näihin voi laittaa kyselyt WHERE item_id on dropboxissa olevan itemin nimi
?>
<table>
<?php
/**
 * Järjestys
 */
$order = "stats";
$direction = "DESC";
if($_POST['order'] && $_POST['order'] != "stats")
    $order = $_POST['order'];
if($_POST['direction'])
    $direction = "ASC";
$where = "";
/**
 * Tietyn pelaajan tilastojen haku
 */
if($_POST['player'] != "all" && $_POST['player'] != null) {
    $where = "WHERE ";
    $where .= $_POST['player'] . " = player_id";
}
/**
 * Tietyn ottelun tilastojen haku
 */
$match = "";
if($_POST['match'] && $_POST['match'] != "all") {
    $match = "AND match_id = " . $_POST['match'];
}

$query = $db->prepare("SELECT player_number, last_name, first_name, (SELECT COUNT(*) FROM statistics_event WHERE item_id = :item_id AND player.player_id = statistics_event.player_id $match) AS stats FROM player $where ORDER BY $order $direction");
$query->bindParam(':item_id', $item_id);
$query->execute();
$result = $query->fetchAll();

foreach($result as $row) {
    print('<tr>');
    $row = array_unique($row);
    foreach($row as $cell) { //String keyt olisi poistettava tai jätettävä huomioimatta
        print('<td>' . $cell .'</td>');
    }
    print('</tr>');
    /*
    print('<tr>');
    print('<td>' . $row['player_number'] . '</td>');
    print('<td>' . $row['last_name'] . '</td>');
    print('<td>' . $row['first_name'] . '</td>');
    print('<td>' . $row['stats'] . '</td>');
    print('</tr>');*/
}

?>
</table>
<form action="index.php" method="post">
	<select name="player">
		<option value="all">Kaikki Pelaajat</option>
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
		<option value="1">Kaikki Tilastomerkinnät</option>
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
		<option value="all">Kaikki Vastustajat</option>
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
		<option value="all">Kaikki Kentät</option>
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
    <select name="match">
        <option value="all">Kaikki Ottelut</option>
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
    <select name="order">
            <option value="stats">Järjestä Tilastojen mukaan</option>
            <option value="player_number">Järjestä Pelinumeron mukaan</option>
<!--            <option value="sex_appeal">Järjestä Seksikkyyden mukaan</option> Kotisivuille toteutettava pisteytysjärjestelmä -->
    </select>
    <input type="checkbox" name="direction">Nouseva järjestys</input>
    <input type="submit" value="Suorita kysely"/>
</form>
<a href="./update.php">update</a>
<?php
include('db/close_db.php');
include 'templates/lower.html';
?>
