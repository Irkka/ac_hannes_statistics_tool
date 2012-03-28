<?php
include ('templates/upper.html');
include('db/connect_db.php');
?>
<a href="index.php">etusivu</a>
<div id="update">
<form name=modify action="update.php" method="post">
    <div id="players">
	<?php
            $players = $db->prepare("SELECT * FROM player");
            $players->execute();
            $result = $players->fetchAll();
            foreach($result as $row) {
                print('<input type="checkbox" name="players[]" value="' . $row['player_id'] . '">');
                print($row['player_number'] . ' ' . $row['last_name']);
                print('</input>');
            }
	?>
    </div>
    <select name="item">
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
	<input type="submit" name="add" value="Lisää"/>
	<input type="submit" name="delete" value="Vähennä" />
</form>
</div>
<div id="dbmod">
<form name="add_item" action="update.php" method="post">
    <input type="text" name="item_name">Tilastokappaleen Nimi</input>
    <input type="submit" name="commit_item" value="Lisää Uusi Tilastoitava Asia">
</form>
<form name="add_field" action="update.php" method="post">
    <input type="text" name="field_name">Kentän Nimi</input>
    <input type="submit" name="commit_field" value="Lisää Uusi Kenttä">
</form>
<form name="add_player" action="update.php" method="post">
    <input type="text" name="last_name">Sukunimi</input>
    <input type="text" name="first_name">Etunimi</input>
    <input type="text" name="player_number">Pelinumero</input>
    <input type="text" name="picture_path">Kuvapolku</input>
    <textarea name="description">Kuvailu</textarea>
    <input type="checkbox" name="active">Aktiivinen?</input
    <input type="submit" name="commit_player" value="Lisää Uusi Pelaaja">
</form>
<form name="add_opponent" action="update.php" method="post">
    <input type="text" name="opponent_name">Vastustajan Nimi</input>
    <input type="submit" name="commit_opponent" value="Lisää Uusi Vastustaja">
</form>
</div>
<?php
if($_POST['add'] && $_POST['players'] != null) {
    $players = $_POST['players']; // tämä ei näy arrayna!!!
    $values = '';
    $first = true;
    foreach($players as $aux) {
        if($first) {
            $values .= '(' . $aux . ',' . $_POST['match'] . ',' . $_POST['item'] . ')';
            $first = false;
        }
        else
            $values .= ', (' . $aux . ',' . $_POST['match'] . ',' . $_POST['item'] . ')';
    }
    $add = $db->prepare("INSERT INTO statistics_event (player_id, match_id, item_id) VALUES $values");
    var_dump($add);
    if($add->execute()) {
        print("Statistics event recorded");
    }
    else
        print("FAILED");

   // var_dump($values);
  //  var_dump($add);
}
/**
 * HUOM! Delete poistaa kaikki samannimiset tilastot pelistä annetuilta henkilöiltä
 */
if($_POST['delete'] && $_POST['players'] != null) {
    $players = $_POST['players']; // tämä ei näy arrayna!!!
    $values = '(';
    $first = true;
    foreach($players as $aux) {
        if($first) {
            $values .= $aux . ' = player_id';
            $first = false;
        }
        else {
            $values .= " OR " . $aux . ' = player_id';
        }
    }
    $values .= ")";
    $match = $_POST['match'];
    $item = $_POST['item'];
    $delete = $db->prepare("DELETE FROM statistics_event WHERE $match = match_id AND $item = item_id AND $values");
    var_dump($delete);
    if($delete->execute())
        print("Deleted!");
    else
        print("FAILED");
}
if($_POST['commit_item'] && $_POST['item_name'] != null && trim($_POST['item_name'] != "")) {
    $item_name = "('" . trim($_POST['item_name']) . "')";
    $add = $db->prepare("INSERT INTO statistics_item (name) VALUES $item_name");
    var_dump($add);
    if($add->execute())
        print("item added!");
    else
        print("failed");
}
/* VAATII useampia kenttiä
if($_POST['commit_player'] && $_POST['player_name'] != null && trim($_POST['player_name'] != "")) {
    $item_name = "('" . trim($_POST['player_name']) . "')";
    $add = $db->prepare("INSERT INTO player (name) VALUES $item_name");
    var_dump($add);
    if($add->execute())
        print("player added!");
    else
        print("failed");
}
*/
if($_POST['commit_field'] && $_POST['field_name'] != null && trim($_POST['field_name'] != "")) {
    $field_name = "('" . trim($_POST['field_name']) . "')";
    $add = $db->prepare("INSERT INTO field (name) values $field_name");
    var_dump($add);
    if($add->execute())
        print("field added!");
    else
        print("failed");
}

if($_POST['commit_opponent'] && $_POST['opponent_name'] != null && trim($_POST['opponent_name'] != "")) {
    $opponent_name = "('" . trim($_POST['opponent_name']) . "')";
    $add = $db->prepare("INSERT INTO opponent (name) VALUES $opponent_name");
    var_dump($add);
    if($add->execute())
        print("opponent added!");
    else
        print("failed");
}
include('db/close_db.php');
include('templates/lower.html');
?>
