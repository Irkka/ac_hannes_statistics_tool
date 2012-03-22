<?php

    /**
     * number of players, teams, events and other to create
     */
    $players = rand(10,20);
    $opponents = rand(7,15);
//SEURAAVAT TÄYTYY MIETTIÄ VÄHÄN TARKEMMIN

    /*== read in the two files into the arrays ==*/
    $adj_array = file("wu_adjs"); 
    $noun_array = file("wu_nouns");
    $team_prefix = file("wu_team_pre");
    $team_suffix = file("wu_team_suf");

    /*== set random seed ==*/ 
    $seed = rand(0, 20000);
    srand($seed); 

?>
