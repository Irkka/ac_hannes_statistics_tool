--pelaajat
insert into player (last_name, first_name, player_number, description) VALUES ('Hakkarainen', 'Ilkka', 7, 'ACH MVP');
insert into player (last_name, first_name, player_number) VALUES ('Pitkänen', 'Timo', 12);
--vastustajajoukkueet
insert into opponent (name) values ('FC Dummy');
insert into opponent (name) values ('FC Herkules');
insert into opponent (name) values ('AC Stoppi/Baari');
--tilastomerkinnät
insert into statistics_item (name) values ('maali');
insert into statistics_item (name) values ('syöttö');
insert into statistics_item (name) values ('oma maali');
insert into statistics_item (name) values ('keltainen');
insert into statistics_item (name) values ('punainen');
insert into statistics_item (name) values ('pelattu_ottelu');
--kentät
insert into field (name, platform) values ('Brahen kenttä', 'nurmi');
insert into field (name, platform) values ('Kulosaaren kenttä', 'hiekka');
--ottelut
insert into match (opponent_id, field_id, date, result, opponent_goals) values ((select opponent_id from opponent where name='FC Dummy'), (select field_id from field where name='Brahen kenttä'), (SELECT CURRENT_DATE), 'w', 2); 
--tilastomerkinnät
insert into statistics_event (player_id, match_id, item_id) values (1, 1, 1); 
insert into statistics_event (player_id, match_id, item_id) values (1, 1, 1); 
insert into statistics_event (player_id, match_id, item_id) values (2, 1, 2); 
insert into statistics_event (player_id, match_id, item_id) values (2, 1, 1); 
insert into statistics_event (player_id, match_id, item_id) values (1, 1, 2); 
insert into statistics_event (player_id, match_id, item_id) values (2, 1, 5); 
insert into statistics_event (player_id, match_id, item_id) values (2, 1, 6); 
insert into statistics_event (player_id, match_id, item_id) values (2, 1, 6); 
