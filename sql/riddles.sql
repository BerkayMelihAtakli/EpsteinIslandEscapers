CREATE TABLE riddles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);

-- Let op, dit is een voorbeeld!
INSERT INTO riddles (riddle, answer, hint, roomId)
VALUES
        