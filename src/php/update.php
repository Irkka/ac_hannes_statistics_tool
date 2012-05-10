<?php
session_id($_COOKIE['session_id']);
session_start();
$_SESSION['match_id'] = $_POST['match'];
if(!isset($_SESSION['logged_in'])) {
    echo "you shouldn't be here";
    die();
}
include ('templates/upper.html');
include('db/connect_db.php');
/*
if(session_id() == '' )
{
    echo 'session_id() empty';
}else{
    echo session_id();
    echo $_SESSION['match_id'] . "<<<<<<<";
}
*/
?>
<div id="messages">
<?php
if($_POST['add'] && $_POST['players'] != null) {
    $players = $_POST['players'];
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
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("Statistics event recorded");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("FAILED");
        print('</p>');
    }
       

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
    //var_dump($delete);
    if($delete->execute()) {
        print('<p class="success">');
        print("Deleted!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("FAILED");
        print('</p>');
    }
}
if($_POST['commit_item'] && $_POST['item_name'] != null && trim(strip_tags($_POST['item_name'])) != "") {
    $item_name = "('" . trim(strip_tags($_POST['item_name'])) . "')";
    $add = $db->prepare("INSERT INTO statistics_item (name) VALUES $item_name");
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("item added!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("failed");
        print('</p>');
    }
}

if($_POST['commit_player'] && trim(strip_tags($_POST['last_name'])) != "" && trim(strip_tags($_POST['first_name'])) != "") {
    $last_name = "'" . trim(strip_tags($_POST['last_name'])) . "'";
    $first_name = "'" . trim(strip_tags($_POST['first_name'])) . "'";
    $player_number = $_POST['player_number'];
    $add = $db->prepare("INSERT INTO player (last_name, first_name, player_number) VALUES ($last_name, $first_name, $player_number)");
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("player added!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("failed");
        print('</p>');
    }
}

if($_POST['commit_field'] && $_POST['field_name'] != null && trim(strip_tags($_POST['field_name'])) != "") {
    $field_name = "('" . trim(strip_tags($_POST['field_name'])) . "')";
    $add = $db->prepare("INSERT INTO field (name) values $field_name");
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("field added!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("failed");
        print('</p>');
    }
}

if($_POST['commit_opponent'] && $_POST['opponent_name'] != null && trim(strip_tags($_POST['opponent_name'])) != "") {
    $opponent_name = "('" . trim(strip_tags($_POST['opponent_name'])) . "')";
    $add = $db->prepare("INSERT INTO opponent (name) VALUES $opponent_name");
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("opponent added!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("failed");
        print('</p>');
    }
}

if($_POST['commit_match']) {
    $opponent_id = $_POST['match_opponent'];
    $field_id = $_POST['match_field'];
    $date = trim(strip_tags($_POST['date']));
    $result = trim(strip_tags($_POST['result']));
    $opponent_goals = $_POST['opponent_goals'];
    $add = $db->prepare("INSERT INTO match (opponent_id, field_id, date, result, opponent_goals) VALUES (" . $opponent_id . ", " . $field_id . ", '" . $date . "', '" . $result . "', " . $opponent_goals . ")");
    //var_dump($add);
    if($add->execute()) {
        print('<p class="success">');
        print("match added!");
        print('</p>');
    }
    else {
        print('<p class="failed">');
        print("failed!");
        print('</p>');
    }
}
?>
</div>
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
                print('</input></br>');
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
                if($_SESSION['match_id'] == $row['match_id'])
                    print('<option selected="selected" value=' . $row['match_id'] . '>');
                else
                    print('<option value=' . $row['match_id'] . '>');
                print($row['date'] . ' ' . $arr[$row['opponent_id']]);
                print('</option>');
            }
        ?>
    </select>
	<input type="submit" name="add" value="Lisää"/>
	<input type="submit" name="delete" value="Poista merkinnät" />
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
    <select name="player_number">
	<?php
		for($i = 1; $i < 100; $i++) {
			print('<option value=' . $i . '>');
			print($i);
			print('</option>');
		}
	?>
    </select>
    <input type="text" name="picture_path">Kuvapolku</input>
    <textarea name="description"></textarea>
    <input type="checkbox" name="active">Aktiivinen?</input>
    <input type="submit" name="commit_player" value="Lisää Uusi Pelaaja" />
</form>
<form name="add_opponent" action="update.php" method="post">
    <input type="text" name="opponent_name">Vastustajan Nimi</input>
    <input type="submit" name="commit_opponent" value="Lisää Uusi Vastustaja">
</form>
<form name="add_match" action="update.php" method="post">
    <select name="match_opponent">
        <?php
        $opponent = $db->prepare("SELECT * FROM opponent");
        $opponent->execute();
        $result = $opponent->fetchAll();
        foreach($result as $row) {
            print('<option value="' . $row['opponent_id'] . '">' . $row['name'] . '</option>');
        }
        ?>
    </select>
    <select name="match_field">
        <?php
        $field = $db->prepare("SELECT * FROM field");
        $field->execute();
        $result = $field->fetchAll();
        foreach($result as $row) {
            print('<option value="' . $row['field_id'] . '">' . $row['name'] . '</option>');
        }
        ?>
    </select>
    <select name="opponent_goals">
        <?php
            for($i = 0; $i < 30; ++$i)
                print('<option value="' . $i . '">' . $i . '</option>');
        ?>
    </select>
    <select name="result">
        <option name="w" value="w">Voitto</option>
        <option name="t" value="t">Tasapeli</option>
        <option name="l" value="l">Häviö</option>
    </select>
    <input type="text" name="date">Pvm(YYYY-MM-DD)</input>
    <input type="submit" name="commit_match" value="Lisää Uusi Ottelu"/>
</form>
</div>
<?php
include('db/close_db.php');
include('templates/lower.html');
?>
