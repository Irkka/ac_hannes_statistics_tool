CREATE TYPE RESULT AS ENUM ('w', 't', 'l');
CREATE TYPE PLATFORM AS ENUM ('nurmi', 'hiekka', 'tekonurmi', 'sisä', 'laivan kansi');

/*
* EI CASCADEA, jos pelaaja poistetaan. Muuten lopputulos muuttuu.
*/
CREATE TABLE player (
player_id SERIAL PRIMARY KEY,
last_name VARCHAR(20) NOT NULL,
first_name VARCHAR(20) NOT NULL,
player_number INTEGER NOT NULL CONSTRAINT illegal_player_number CHECK (player_number > 0 AND player_number < 100),
picture_path VARCHAR(20) DEFAULT 'default',
description VARCHAR(200) DEFAULT 'no description... yet.',
active BOOLEAN NOT NULL DEFAULT true, --Kaikki non-active playerit esitetään tilastosivuilla yhtenä pelaajana 'muut'
CONSTRAINT player_exists UNIQUE(last_name, first_name)
);

CREATE TABLE opponent (
opponent_id SERIAL PRIMARY KEY,
name VARCHAR(20) CONSTRAINT opponent_exists UNIQUE
);

CREATE TABLE field (
field_id SERIAL PRIMARY KEY,
name VARCHAR(50) CONSTRAINT field_exists UNIQUE,
platform PLATFORM
);

CREATE TABLE match (
match_id SERIAL PRIMARY KEY,
opponent_id INTEGER REFERENCES opponent,
field_id INTEGER REFERENCES field,
date DATE NOT NULL, --default today() tjsp
result RESULT NOT NULL,
opponent_goals INTEGER NOT NULL CONSTRAINT positive_goal_amount CHECK (opponent_goals >= 0)
);

CREATE TABLE statistics_item (
item_id SERIAL PRIMARY KEY,
name VARCHAR(20) CONSTRAINT item_exists UNIQUE
);

CREATE TABLE statistics_event (
statistics_event_id SERIAL PRIMARY KEY,
player_id INTEGER REFERENCES player ON DELETE RESTRICT, --player-taulusta ei ole mahdollista poistaa pelaajaa, jolla on tilastotapahtumia -> Pelaajalle laitetaan active=false
match_id INTEGER REFERENCES match ON DELETE CASCADE, --jos ottelu poistetaan match-taulusta, poistuvat myös kaikki otteluun liitetyt tilastomerkinnät
item_id INTEGER REFERENCES statistics_item ON DELETE CASCADE --jos tilastoitava asia poistetaan statistics_item-taulusta, poistetaan kaikki vastaavat tilastomerkinnät tuolle itemille
);

