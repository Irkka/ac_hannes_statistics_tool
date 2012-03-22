<?php
include 'connect_db.php';
include '/home/ilkkahak/htdocs/ach/wuname/generate.php';

for($i = 0; $i < $players; ++$i) {
    $arnd = rand(0,sizeof($adj_array)-1);
    $nrnd = rand(0,sizeof($noun_array)-1);
    $wuname = "$adj_array[$arnd] $noun_array[$nrnd]";
print($adj_array[$arnd] . " " . $noun_array[$nrnd]);
    $db->query("INSERT INTO player (last_name, first_name, player_number) VALUES ($adj_array[$arnd], $noun_array[$nrnd], rand(0-79))");
}
$db->query("insert into player (last_name, first_name, player_number, description) VALUES ('Hakkarainen', 'Ilkka', 7, 'ACH MVP')");

for($i = 0; $i < $opponents; ++$i) {
    $tprnd = rand(0,sizeof($team_prefix)-1);
    $tsrnd = rand(0,sizeof($team_suffix)-1);
print($team_prefix[$tprnd] . " " . $team_suffix[$tsrnd]);

    $db->query("INSERT INTO opponent (name) values ($team_prefix[$tprnd]" . " " . "$team_suffix[$tsrnd])");
}
$stats = array('maali', 'syöttö', 'oma maali', 'keltainen', 'punainen', 'pelattu_ottelu');
$stats_count = sizeof($stats);
while(!empty($stats)) {
    $item = array_pop($stats);
    $db->query("INSERT INTO statistics_item (name) VALUES ($item)");
}
$fields = array('Brahen kenttä' => 'nurmi', 'Kulosaaren kenttä' => 'hiekka', 'Pallomylly' => 'tekonurmi');
$field_count = sizeof($fields);
while(!empty($fields)) {
    $item = array_pop($fields);
    $db->query("INSERT INTO field (name, platform) VALUES (key($item), $item)");
}

$matches = rand(10,30);
for($i = 0; $matches > $i; $i++) {
$db->query("INSERT INTO match (opponent_id, field_id, date, result, opponent_goals) VALUES (rand(1,$opponents), rand(1, $field_count), date(), rand(1,3), rand(0,10))");
}

$events = rand(50,100);
for($i = 0; $i < $events; $i++) {
$db->query("INSERT INTO statistics_event (player_id, match_id, item_id) values (rand(1,$players), rand(1,$matches), rand(1, $stats_count))");

}

include 'close_db.php';
?>
