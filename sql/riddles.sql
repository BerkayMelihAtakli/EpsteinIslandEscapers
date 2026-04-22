DROP TABLE IF EXISTS riddles;
DROP TABLE IF EXISTS question;


CREATE TABLE question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);


INSERT INTO question (riddle, hint, answer, roomId) VALUES
-- Room 1
('I have cities, but no houses. I have mountains, but no trees. What am I?', 'Take a look at the paper map on the wall.', 'map', 1),
('The more of me there is, the less you see. What am I?', 'Try turning off the lights.', 'darkness', 1),
('I always run, but have no legs. I have a mouth, but never speak. What am I?', 'Think of flowing water.', 'river', 1),

-- Room 2 
('I am light as a feather, yet even the strongest man cannot hold me for long. What am I?', 'You are doing it right now while reading.', 'breath', 2),
('What has keys but cannot open locks?', 'You often find it in a living room or music school.', 'piano', 2),
('What gets wetter the more it dries?', 'You use it after showering.', 'towel', 2),

-- Room 3
('What has a face and two hands, but no arms or legs?', 'It ticks...', 'clock', 3),
('I have holes on the top and bottom, left and right, yet I still hold water. What am I?', 'You can find me in the kitchen.', 'sponge', 3),
('What belongs to you, but is used by others more than yourself?', 'It is on your passport.', 'name', 3);