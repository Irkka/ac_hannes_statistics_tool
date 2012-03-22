<?php

    /**
     * number of players, teams, events and other to create
     */
    $players = rand(10,20);
    $opponents = rand(7,15);
//SEURAAVAT TÄYTYY MIETTIÄ VÄHÄN TARKEMMIN

    /*== read in the two files into the arrays ==*/
    $adj_array = file("/home/ilkkahak/htdocs/ach/wuname/wu_adjs"); 
    $noun_array = file("/home/ilkkahak/htdocs/ach/wuname/wu_nouns");
    $team_prefix = file("/home/ilkkahak/htdocs/ach/wuname/wu_team_pre");
    $team_suffix = file("/home/ilkkahak/htdocs/ach/wuname/wu_team_suf");

    /*== set random seed ==*/ 
    $seed = rand(0, 20000);
    srand($seed); 

?>
