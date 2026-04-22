<?php

declare(strict_types=1);

function ensureProjectSchema(?PDO $db_connection): void
{
    if (!$db_connection instanceof PDO) {
        return;
    }

    $db_connection->exec(
        'CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_name VARCHAR(100) NOT NULL,
            member1 VARCHAR(100) NOT NULL,
            member2 VARCHAR(100) NOT NULL,
            member3 VARCHAR(100) DEFAULT NULL,
            member4 VARCHAR(100) DEFAULT NULL,
            score INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            finished_at DATETIME NULL,
            elapsed_seconds INT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $db_connection->exec(
        'CREATE TABLE IF NOT EXISTS question (
            id INT AUTO_INCREMENT PRIMARY KEY,
            riddle VARCHAR(255) NOT NULL,
            answer VARCHAR(100) NOT NULL,
            hint VARCHAR(255) DEFAULT NULL,
            roomId INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $translations = [
        ['Ik heb steden, maar geen huizen. Ik heb bergen, maar geen bomen. Wat ben ik?', 'I have cities, but no houses. I have mountains, but no trees. What am I?', 'kaart', 'map', 'Kijk eens op de muur naar die papieren kaart.', 'Take a look at the paper map on the wall.', 1],
        ['Hoe meer er van mij is, hoe minder je ziet. Wat ben ik?', 'The more of me there is, the less you see. What am I?', 'duisternis', 'darkness', 'Doe het licht maar eens uit.', 'Try turning off the lights.', 1],
        ['Ik loop altijd, maar heb geen benen. Ik heb een mond, maar spreek nooit. Wat ben ik?', 'I always run, but have no legs. I have a mouth, but never speak. What am I?', 'rivier', 'river', 'Denk aan kabbelend water.', 'Think of flowing water.', 1],
        ['Ik ben licht als een veer, maar zelfs de sterkste man kan mij niet lang vasthouden. Wat ben ik?', 'I am light as a feather, yet even the strongest man cannot hold me for long. What am I?', 'adem', 'breath', 'Je doet het nu terwijl je leest.', 'You are doing it right now while reading.', 2],
        ['Wat heeft sleutels maar kan geen sloten openen?', 'What has keys but cannot open locks?', 'piano', 'piano', 'Het staat vaak in een woonkamer of muziekschool.', 'You often find it in a living room or music school.', 2],
        ['Wat wordt natter naarmate het meer droogt?', 'What gets wetter the more it dries?', 'handdoek', 'towel', 'Je gebruikt dit na het douchen.', 'You use it after showering.', 2],
        ['Wat heeft een gezicht en twee handen, maar geen armen of benen?', 'What has a face and two hands, but no arms or legs?', 'klok', 'clock', 'Het tikt...', 'It ticks...', 3],
        ['Ik heb gaten aan de bovenkant en de onderkant, aan de linkerkant en de rechterkant, en toch houd ik water vast. Wat ben ik?', 'I have holes on the top and bottom, left and right, yet I still hold water. What am I?', 'spons', 'sponge', 'Je vindt me in de keuken.', 'You can find me in the kitchen.', 3],
        ['Wat is van jou, maar wordt door anderen veel vaker gebruikt dan door jezelf?', 'What belongs to you, but is used by others more than yourself?', 'naam', 'name', 'Het staat op je paspoort.', 'It is on your passport.', 3],
        ['Ik spreek zonder mond en luister zonder oren. Ik heb geen lichaam, maar ik kom tot leven met de wind. Wat ben ik?', 'I speak without a mouth and hear without ears. I have no body, but I come to life with wind. What am I?', 'echo', 'echo', 'Het is iets dat je hoort als het je woorden herhaalt.', 'It is something that repeats your words back to you.', 3],
        ['Hoe meer je van mij wegneemt, hoe groter ik word. Wat ben ik?', 'The more you take away from me, the bigger I become. What am I?', 'gat', 'hole', 'Denk aan graven of lege ruimtes.', 'Think about digging or empty spaces.', 3],
        ['Ik heb toetsen maar geen sloten. Ik heb een spatie maar geen kamer. Je kunt mij betreden, maar je kunt niet naar buiten. Wat ben ik?', 'I have keys but no locks. I have space but no room. You can enter, but you cannot go outside. What am I?', 'toetsenbord', 'keyboard', 'Je gebruikt mij om te typen.', 'You use me to type.', 3],
    ];

    $translateStmt = $db_connection->prepare(
        'UPDATE question
         SET riddle = :new_riddle,
             answer = :new_answer,
             hint = :new_hint
         WHERE riddle = :old_riddle
           AND roomId = :room_id'
    );

    foreach ($translations as $row) {
        $translateStmt->execute([
            ':new_riddle' => $row[1],
            ':new_answer' => $row[3],
            ':new_hint' => $row[5],
            ':old_riddle' => $row[0],
            ':room_id' => $row[6],
        ]);
    }

    $stripLabels = [
        ['[Cult Lock] I have no face, yet I scream through iron halls at midnight. Torches die when I breathe, and the sea kneels under my rage. What am I?', 'I have no face, yet I scream through iron halls at midnight. Torches die when I breathe, and the sea kneels under my rage. What am I?', 0],
        ['[Room 1 - Omen Cipher] Decode: VIEPMXC / WXEVXW / XS / FPIIH', 'Decode: VIEPMXC / WXEVXW / XS / FPIIH', 1],
        ['[Room 1 - Sigil Disarm] Repeat the 9-rune route in exact order.', 'Repeat the 9-rune route in exact order.', 1],
        ['[Room 1 - Final Seal] Enter the 5-digit forged seal code.', 'Enter the 5-digit forged seal code.', 1],
        ['[Room 2 - Exit Key Trial] Pick the correct key color to open the exit.', 'Pick the correct key color to open the exit.', 2],
    ];

    $stripLabelsStmt = $db_connection->prepare(
        'UPDATE question
         SET riddle = :new_riddle
         WHERE riddle = :old_riddle
           AND roomId = :room_id'
    );

    foreach ($stripLabels as $row) {
        $stripLabelsStmt->execute([
            ':old_riddle' => $row[0],
            ':new_riddle' => $row[1],
            ':room_id' => $row[2],
        ]);
    }

    // Room 1 gameplay is scripted; remove misleading classic Q&A rows from admin DB list.
    $removeRoom1Core = $db_connection->prepare(
        'DELETE FROM question
         WHERE roomId = 1
           AND riddle IN (
             :r1_en, :r2_en, :r3_en,
             :r1_nl, :r2_nl, :r3_nl
           )'
    );
    $removeRoom1Core->execute([
        ':r1_en' => 'I have cities, but no houses. I have mountains, but no trees. What am I?',
        ':r2_en' => 'The more of me there is, the less you see. What am I?',
        ':r3_en' => 'I always run, but have no legs. I have a mouth, but never speak. What am I?',
        ':r1_nl' => 'Ik heb steden, maar geen huizen. Ik heb bergen, maar geen bomen. Wat ben ik?',
        ':r2_nl' => 'Hoe meer er van mij is, hoe minder je ziet. Wat ben ik?',
        ':r3_nl' => 'Ik loop altijd, maar heb geen benen. Ik heb een mond, maar spreek nooit. Wat ben ik?',
    ]);

    $seedRiddles = [
        // Lobby / Join the cult
        ['I have no face, yet I scream through iron halls at midnight. Torches die when I breathe, and the sea kneels under my rage. What am I?', 'storm,tempest,thunderstorm,squall,gale', 'Use one weather-word answer.', 0],

        // Room 1 scripted enigmas
        ['Decode: VIEPMXC / WXEVXW / XS / FPIIH', 'reality starts to bleed', 'Each letter goes back by 4.', 1],
        ['Repeat the 9-rune route in exact order.', 'b4,b1,b7,b2,b9,b3,b8,b5,b6', 'Preview limit is 5. Then repeat from memory.', 1],
        ['Enter the 5-digit forged seal code.', '45138', 'Use the morse hint and forged-code logic.', 1],

        // Room 2/3 core riddles (English)
        ['I am light as a feather, yet even the strongest man cannot hold me for long. What am I?', 'breath', 'You are doing it right now while reading.', 2],
        ['What has keys but cannot open locks?', 'piano', 'You often find it in a living room or music school.', 2],
        ['What gets wetter the more it dries?', 'towel', 'You use it after showering.', 2],
        ['What has a face and two hands, but no arms or legs?', 'clock', 'It ticks...', 3],
        ['I have holes on the top and bottom, left and right, yet I still hold water. What am I?', 'sponge', 'You can find me in the kitchen.', 3],
        ['What belongs to you, but is used by others more than yourself?', 'name', 'It is on your passport.', 3],

        // Room 2 extra locker riddles + key trial game
        ['I speak without a mouth and hear without ears. I have no body, but I come to life with wind. What am I?', 'echo', 'Something that repeats your words back to you.', 2],
        ['The more you take, the more you leave behind. What am I?', 'footsteps', 'Think about walking or running.', 2],
        ['Pick the correct key color to open the exit.', 'gold', 'The gold key from locker C is correct.', 2],

        // Room 3 in-code riddles
        ['I speak without a mouth and hear without ears. I have no body, but I come to life with wind. What am I?', 'echo', 'It is something that repeats your words back to you.', 3],
        ['The more you take away from me, the bigger I become. What am I?', 'hole', 'Think about digging or empty spaces.', 3],
        ['I have keys but no locks. I have space but no room. You can enter, but you cannot go outside. What am I?', 'keyboard', 'You use me to type.', 3],
    ];

    $seedInsert = $db_connection->prepare(
        'INSERT INTO question (riddle, answer, hint, roomId)
         VALUES (:riddle, :answer, :hint, :roomId)'
    );
    $seedExists = $db_connection->prepare(
        'SELECT id FROM question WHERE riddle = :riddle AND roomId = :roomId LIMIT 1'
    );

    foreach ($seedRiddles as $row) {
        $seedExists->execute([
            ':riddle' => $row[0],
            ':roomId' => $row[3],
        ]);

        if ($seedExists->fetch()) {
            continue;
        }

        $seedInsert->execute([
            ':riddle' => $row[0],
            ':answer' => $row[1],
            ':hint' => $row[2],
            ':roomId' => $row[3],
        ]);
    }

    $db_connection->exec(
        'CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_id INT NULL,
            rating TINYINT NOT NULL,
            difficulty VARCHAR(30) NOT NULL,
            feedback TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_reviews_team
                FOREIGN KEY (team_id) REFERENCES teams(id)
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}
