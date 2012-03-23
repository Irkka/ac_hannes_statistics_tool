<?php
include 'connect_db.php';
include 'generate.php';
for($i = 0; $i < $players; ++$i) {
    $arnd = rand(0,sizeof($adj_array)-1);
    $nrnd = rand(0,sizeof($noun_array)-1);

    $query = $db->prepare("INSERT INTO player (last_name, first_name, player_number) VALUES (:last, :first, :number)");
    $query->bindParam(':last', $last_name);
    $query->bindParam(':first', $first_name);
    $query->bindParam(':number', $number);

    $last_name = $adj_array[$arnd];
    $first_name = $noun_array[$nrnd];
    $number = rand(0, 79);

    $query->execute();
}

for($i = 0; $i < $opponents; ++$i) {
    $tprnd = rand(0,sizeof($team_prefix)-1);
    $tsrnd = rand(0,sizeof($team_suffix)-1);

    $query = $db->prepare("INSERT INTO opponent (name) values (:team)");
    $query->bindParam(':team', $team);
    $team = '';
    if($team_prefix[$tprnd] != "\n") {
        $team = str_replace("\n", ' ', $team_prefix[$tprnd]);
    }
    $team .= str_replace("\n", '', $team_suffix[$tsrnd]);
    $query->execute();
}

$stats = array('maali', 'syöttö', 'oma maali', 'keltainen', 'punainen', 'pelattu_ottelu');
$stats_count = sizeof($stats);
while(!empty($stats)) {
    $item = array_pop($stats);
    $query = $db->prepare("INSERT INTO statistics_item (name) VALUES (:item)");
    $query->bindParam(':item', $item);
    $query->execute();
}
$fields = array('Brahen kenttä' => 'nurmi', 'Kulosaaren kenttä' => 'hiekka', 'Pallomylly' => 'tekonurmi');
$field_count = sizeof($fields);
while(!empty($fields)) {
    $item_name = array_keys($fields);
    $item_name = array_pop($item_name);
    $item = array_pop($fields);
    $query = $db->prepare("INSERT INTO field (name, platform) VALUES (:item_name, :item)");
    $query->bindparam(':item', $item);
    $query->bindParam('item_name', $item_name);
    $query->execute();
}

$matches = rand(10,30);
for($i = 0; $matches > $i; $i++) {
    $query = $db->prepare("insert into match (opponent_id, field_id, result, opponent_goals) values (:opponent, :field, :result, :goals)");
    $query->bindparam(':opponent', $opponent);
    $query->bindparam(':field', $field);
    $query->bindparam(':result', $result);
    $query->bindparam(':goals', $goals);

    $result_pool = array('w', 't', 'l');
    $opponent = rand(1,$opponents-1);
    $field = rand(1, $field_count-1);
    $result = $result_pool[rand(0, sizeof($result_pool)-1)];
    $goals = rand(0,10);
    $query->execute();
}

$events = rand(50,100);
for($i = 0; $i < $events; $i++) {
    $query = $db->prepare("INSERT INTO statistics_event (player_id, match_id, item_id) values (:player, :match, :stat)");
    $query->bindparam(':player', $player);
    $query->bindparam(':match', $match);
    $query->bindparam(':stat', $stat);

    $player = rand(1, $players);
    $match = rand(1, $matches);
    $stat = rand(1, $stats_count);
    $query->execute();

}
include 'close_db.php';
?>
