CREATE DATABASE IF NOT EXISTS epsteinislandescapers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE epsteinislandescapers;

DROP TABLE IF EXISTS teams;
DROP TABLE IF EXISTS question;

CREATE TABLE question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);

INSERT INTO question (id, riddle, answer, hint, roomId) VALUES
(1, 'Ik heb steden, maar geen huizen. Ik heb bergen, maar geen bomen. Wat ben ik?', 'kaart', 'Kijk eens op de muur naar die papieren kaart.', 1),
(2, 'Hoe meer er van mij is, hoe minder je ziet. Wat ben ik?', 'duisternis', 'Doe het licht maar eens uit.', 1),
(3, 'Ik loop altijd, maar heb geen benen. Ik heb een mond, maar spreek nooit. Wat ben ik?', 'rivier', 'Denk aan kabbelend water.', 1),
(4, 'Ik ben licht als een veer, maar zelfs de sterkste man kan mij niet lang vasthouden. Wat ben ik?', 'adem', 'Je doet het nu terwijl je leest.', 2),
(5, 'Wat heeft sleutels maar kan geen sloten openen?', 'piano', 'Het staat vaak in een woonkamer of muziekschool.', 2),
(6, 'Wat wordt natter naarmate het meer droogt?', 'handdoek', 'Je gebruikt dit na het douchen.', 2),
(7, 'Wat heeft een gezicht en twee handen, maar geen armen of benen?', 'klok', 'Het tikt...', 3),
(8, 'Ik heb gaten aan de bovenkant en de onderkant, aan de linkerkant en de rechterkant, en toch houd ik water vast. Wat ben ik?', 'spons', 'Je vindt me in de keuken.', 3),
(9, 'Wat is van jou, maar wordt door anderen veel vaker gebruikt dan door jezelf?', 'naam', 'Het staat op je paspoort.', 3);

ALTER TABLE question AUTO_INCREMENT = 10;

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    member1 VARCHAR(100) NOT NULL,
    member2 VARCHAR(100) NOT NULL,
    member3 VARCHAR(100) NULL,
    member4 VARCHAR(100) NULL,
    score INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO teams (id, team_name, member1, member2, member3, member4, score, created_at) VALUES
(1, 'ataturk', 'mmq', 'mmm', NULL, NULL, 0, '2026-04-17 10:44:27'),
(2, 'ataturk', 'mmq', 'mmm', NULL, NULL, 0, '2026-04-17 10:46:43'),
(3, 'ataturk', 'q', 'q', NULL, NULL, 0, '2026-04-17 10:47:55'),
(4, 'babaci', 'q', 'q', NULL, NULL, 0, '2026-04-17 10:48:53'),
(5, 'qemrwen', 'nkekr', 'k', NULL, NULL, 0, '2026-04-17 10:50:29'),
(6, 'ataturk', 'Q', 'Q', NULL, NULL, 0, '2026-04-17 10:56:59'),
(7, 'nqwenqwr', 'q', 'q', NULL, NULL, 0, '2026-04-17 10:58:50'),
(8, 'q', 'q', '', NULL, NULL, 0, '2026-04-17 11:04:26');

ALTER TABLE teams AUTO_INCREMENT = 9;
