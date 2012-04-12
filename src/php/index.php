<?php
include 'templates/upper.html';
include('db/connect_db.php');

//Näihin voi laittaa kyselyt WHERE item_id on dropboxissa olevan itemin nimi
?>
<a href="./update.php">update</a>
<div id="stat_table">
<table>
<?php
/**
 * Järjestys
 */
$direction = "DESC";
if($_POST['order'] == "stats")
    $order = $_POST['item'];
else
    $order = "pelinumero";
if($_POST['direction'])
    $direction = "ASC";
$where = "player.active != false"; //jos joku ei halua näkyä enää tilastoissa
/**
 * Tietyn pelaajan tilastojen haku
 */
if($_POST['player'] != "all" && $_POST['player'] != null) {
    $where .= " AND " . $_POST['player'] . " = player_id";
}

if($where != "") {
    $where = "WHERE " . $where;
}

/**
 * Tietyn ottelun tilastojen haku
 */
$match = "";
if($_POST['match'] && $_POST['match'] != "all") {
    $match .= "AND match_id = " . $_POST['match'];
}

if($_POST['field'] && $_POST['field'] != "all") {
    $field = $_POST['field'];
    $match_count = $db->prepare("SELECT * FROM match WHERE field_id = $field");
    $match_count->execute();
    $result = $match_count->fetchAll();
    $first = true;
    if(!empty($result)) {
    foreach($result as $row) {
        if($first) {
            $match .= "AND (match_id = " . $row['match_id'];
            $first = false;
        }
        $match .= " OR match_id = " . $row['match_id'];
    }
    $match .= ")";
    }
    else { //kentällä ei olla vielä pelattu
       $match = "AND match_id = 999";
    }
}

if($_POST['opponent'] && $_POST['opponent'] != "all") {
    $opponent = $_POST['opponent'];
    $match_count = $db->prepare("SELECT * FROM match WHERE opponent_id = $opponent");
    $match_count->execute();
    $result = $match_count->fetchAll();
    $first = true;
    if(!empty($result)) {
        foreach($result as $row) {
            if($first) {
                $match .= "AND (match_id = " . $row['match_id'];
                $first = false;
            }
            $match .= " OR match_id = " . $row['match_id'];
        }
        $match .= ")";
    }
    else { //vastustajaa vastaan ei olla vielä pelattu
        $match = "AND match_id = 999";
    }
}
//$query = $db->prepare("SELECT player_number AS pelaajanumero, last_name AS sukunimi, first_name AS etunimi, (SELECT COUNT(*) FROM statistics_event WHERE item_id = :item_id AND player.player_id = statistics_event.player_id $match) AS stats FROM player $where ORDER BY $order $direction");
//$query->bindParam(':item_id', $item_id);
$items = $db->prepare("SELECT * FROM statistics_item;");
$items->execute();
$items_result = $items->fetchAll();
$stat_query = "";
$first = true;
foreach($items_result as $item) {
    if($first) {
        $stat_query .= "(SELECT COUNT(*) FROM statistics_event WHERE item_id = " . $item['item_id'] . " AND player.player_id = statistics_event.player_id " . $match . ") AS " . $item['name'];
        $first = false;
    }
    else
        $stat_query .= ", (SELECT COUNT(*) FROM statistics_event WHERE item_id = " . $item['item_id'] . " AND player.player_id = statistics_event.player_id " . $match . ") AS " . $item['name'];
}

$query = $db->prepare("SELECT player_number AS pelinumero, last_name AS sukunimi, first_name AS etunimi, $stat_query FROM player $where ORDER BY $order $direction");
var_dump($query);
$query->execute();
$result = $query->fetchAll();

$header = array_unique(array_keys($result[0]));
foreach($header as $aux) {
    if(is_numeric($aux))
        continue;
    print('<th>' . $aux . '</th>');
}
    foreach($result as $row) {
        foreach($row as $key=>$var){
            if(is_numeric($key)){
                unset($row[$key]);
            }
        }
    print('<tr>');
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
</div>
<div id="filter">
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
        <?php
            $stat_items = $db->prepare("SELECT * FROM statistics_item");
            $stat_items->execute();
            $result = $stat_items->fetchAll();
            foreach($result as $row) {
                print('<option value=' . $row['name'] . '>');
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
            <option value="stats">Järjestä Valitun Tilaston mukaan</option>
            <option value="player_number">Järjestä Pelinumeron mukaan</option>
<!--            <option value="sex_appeal">Järjestä Seksikkyyden mukaan</option> Kotisivuille toteutettava pisteytysjärjestelmä -->
    </select>
    <input type="checkbox" name="direction">Nouseva järjestys</input>
    <input type="submit" value="Suorita kysely"/>
</form>
</div>

<?php
include('db/close_db.php');
include 'templates/lower.html';
?>
