CREATE TABLE riddles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);


INSERT INTO question (riddle, hint, answer, roomId) VALUES
-- Kamer 1
('Ik heb steden, maar geen huizen. Ik heb bergen, maar geen bomen. Wat ben ik?', 'Kijk eens op de muur naar die papieren kaart.', 'kaart', 1),
('Hoe meer er van mij is, hoe minder je ziet. Wat ben ik?', 'Doe het licht maar eens uit.', 'duisternis', 1),
('Ik loop altijd, maar heb geen benen. Ik heb een mond, maar spreek nooit. Wat ben ik?', 'Denk aan kabbelend water.', 'rivier', 1),

-- Kamer 2 
('Ik ben licht als een veer, maar zelfs de sterkste man kan mij niet lang vasthouden. Wat ben ik?', 'Je doet het nu terwijl je leest.', 'adem', 2),
('Wat heeft sleutels maar kan geen sloten openen?', 'Het staat vaak in een woonkamer of muziekschool.', 'piano', 2),
('Wat wordt natter naarmate het meer droogt?', 'Je gebruikt dit na het douchen.', 'handdoek', 2);

-- Kamer 3
('Wat heeft een gezicht en twee handen, maar geen armen of benen?', 'Het tikt...', 'klok', 3),
('Ik heb gaten aan de bovenkant en de onderkant, aan de linkerkant en de rechterkant, en toch houd ik water vast. Wat ben ik?', 'Je vindt me in de keuken.', 'spons', 3),
('Wat is van jou, maar wordt door anderen veel vaker gebruikt dan door jezelf?', 'Het staat op je paspoort.', 'naam', 3);