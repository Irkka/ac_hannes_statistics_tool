<?php
session_start();
if($_POST['logout'])
    logout();
setcookie("session_id", session_id());
include 'templates/upper.html';
include('db/connect_db.php');

?>
<div id="login_box">
<?php
/*    if(session_id() == '' )
    {
        echo 'session_id() empty';
    }else{
        echo session_id();
    }
*/
if($_POST['login']) {
    if(loginIsGood($db)) {
        echo "<p>Login good!</p>";
    }
    else
        echo "<p>Login failed</p>";
}
if(isset($_SESSION['logged_in'])) {
    echo '<a href="./update.php">update</a>';
    echo '<form name="logout_form" method="post" action="index.php">';
    echo '<input type="submit" name="logout" value="Logout"/>';
    echo '</form>';
}
else {
echo '<form name="login_form" method="post" action="index.php">';
echo '<input type="text" name="username" value="username"/>';
echo '<input type="password" name="password" value="password"/>';
echo '<input type="submit" name="login" value="Login"/>';
echo '</form>';
}
?>
</div>
<?php

?>
<div id="stat_table">
<table>
<?php
/**
 * Järjestys
 */
$direction = "DESC";

$_SESSION['item'] = $_POST['item'];
if($_POST['item'])
    $order = $_POST['item'];
else
    $order = "pelinumero";

if($_POST['direction']) {
    $direction = "ASC";
}

$where = "player.active != false"; //jos joku ei halua näkyä enää tilastoissa
/**
 * Tietyn pelaajan tilastojen haku
 */
if($_POST['player'] != "all" && $_POST['player'] != null) {
    $_SESSION['player'] = $_POST['player'];
    $where .= " AND " . $_POST['player'] . " = player_id";
}
else
    unset($_SESSION['player']);

if($where != "") {
    $where = "WHERE " . $where;
}

/**
 * Tietyn ottelun tilastojen haku
 */
$match = "";
if($_POST['match'] && $_POST['match'] != "all") {
    $_SESSION['match'] = $_POST['match'];
    $match .= "AND match_id = " . $_POST['match'];
}
else
    unset($_SESSION['match']);

if($_POST['field'] && $_POST['field'] != "all") {
    $_SESSION['field'] = $_POST['field'];
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
else
    unset($_SESSION['field']);

if($_POST['opponent'] && $_POST['opponent'] != "all") {
    $_SESSION['opponent'] = $_POST['opponent'];
    $opponent = $_POST['opponent'];
    $match_count = $db->prepare("SELECT * FROM match WHERE opponent_id = $opponent");
    $match_count->execute();
    $result = $match_count->fetchAll();
    $first = true;
    if(!empty($result)) {
        foreach($result as $row) {
            if($first) {
                $match .= " AND (match_id = " . $row['match_id'];
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
else
    unset($_SESSION['opponent']);

/**
 * Creates the statistics table
 */
generateTable($db, $match, $where, $order, $direction);
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
                if($_SESSION['player'] == $row['player_id'])
                    print('<option value=' . $row['player_id'] . ' selected="selected">');
                else
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
                if($_SESSION['item'] == $row['name'])
                    print('<option value=' . $row['name'] . ' selected="selected">');
                else
                    print('<option value=' . $row['name'] . '>');
                print($row['name']);
                print('</option>');
            }
        if($_SESSION['item'] == "player_number")
            print('<option value="player_number" selected="selected">pelinumero</option>');
        else
            print('<option value="player_number">pelinumero</option>');
        ?>

    </select>
    <select name="opponent">
		<option value="all">Kaikki Vastustajat</option>
        <?php
            $opponent = $db->prepare("SELECT * FROM opponent");
            $opponent->execute();
            $result = $opponent->fetchAll();
            foreach($result as $row) {
                if($_SESSION['opponent'] == $row['opponent_id'])
                    print('<option value=' . $row['opponent_id'] . ' selected="selected">');
                else
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
                if($_SESSION['field'] == $row['field_id'])
                    print('<option value=' . $row['field_id'] . ' selected="selected">');
                else
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
            if($_SESSION['match'] == $row['match_id'])
                print('<option value=' . $row['match_id'] . ' selected="selected">');
            else
                print('<option value=' . $row['match_id'] . '>');
            print($row['date'] . ' ' . $arr[$row['opponent_id']]);
            print('</option>');
        }
        ?>
    </select>
    <input type="checkbox" name="direction">Nouseva järjestys</input>
    <input type="submit" value="Suorita kysely"/>
</form>
</div>

<?php
include('db/close_db.php');
include 'templates/lower.html';

function loginIsGood($db) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $db->prepare("SELECT * FROM login WHERE username LIKE '{$username}' AND passwd LIKE '" . md5($password) . "'");
    print("\n");
//    echo md5($password);
    $query->execute();
    $result = $query->fetchAll();

    if(count($result) == 1) {
        $_SESSION['logged_in'] = true;
        return true;
    }
    else
        return false;
}

function logout() {
    unset($_SESSION['logged_in']);
    //session_start();
    //session_destroy();
}

function generateTable($db, $match, $where, $order, $direction) {

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
    //var_dump($query);
    $query->execute();
    $result = $query->fetchAll();
    //var_dump($result);
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
}
?>
