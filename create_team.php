<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'dbcon.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = trim($_POST['team_name'] ?? '');
    $member1 = trim($_POST['member1'] ?? '');
    $member2 = trim($_POST['member2'] ?? '');
    $member3 = trim($_POST['member3'] ?? '');
    $member4 = trim($_POST['member4'] ?? '');

    if ($team_name === '') {
        $errors[] = 'Please enter a team name.';
    }
    if ($member1 === '') {
        $errors[] = 'Please enter the name of member 1.';
    }

    if (empty($errors)) {
        $db_connection->exec(
            'CREATE TABLE IF NOT EXISTS teams (
                id INT AUTO_INCREMENT PRIMARY KEY,
                team_name VARCHAR(100) NOT NULL,
                member1 VARCHAR(100) NOT NULL,
                member2 VARCHAR(100) NOT NULL,
                member3 VARCHAR(100) DEFAULT NULL,
                member4 VARCHAR(100) DEFAULT NULL,
                score INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $insert = $db_connection->prepare(
            'INSERT INTO teams (team_name, member1, member2, member3, member4) VALUES (:team_name, :member1, :member2, :member3, :member4)'
        );

        $insert->execute([
            ':team_name' => $team_name,
            ':member1' => $member1,
            ':member2' => $member2,
            ':member3' => $member3 ?: null,
            ':member4' => $member4 ?: null,
        ]);

        $_SESSION['team_id'] = $db_connection->lastInsertId();
        $_SESSION['team_name'] = $team_name;

        header('Location: /EpsteinIslandEscapers/rooms/room_1.php');
        exit;
    }
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>

<main class="team-form-page">
    <section class="team-form-container">
        <h1>Create your team</h1>
        <p>Enter a team name and at least two team members.</p>

        <?php if (!empty($errors)): ?>
            <div class="form-error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo escape($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/EpsteinIslandEscapers/create_team.php" class="team-form">
            <label for="team_name">Team Name</label>
            <input id="team_name" name="team_name" type="text" value="<?php echo escape($team_name ?? ''); ?>" required>

            <label for="member1">Member 1</label>
            <input id="member1" name="member1" type="text" value="<?php echo escape($member1 ?? ''); ?>" required>

            <label for="member2">Member 2</label>
            <input id="member2" name="member2" type="text" value="<?php echo escape($member2 ?? ''); ?>">

            <label for="member3">Member 3 (optional)</label>
            <input id="member3" name="member3" type="text" value="<?php echo escape($member3 ?? ''); ?>">

            <label for="member4">Member 4 (optional)</label>
            <input id="member4" name="member4" type="text" value="<?php echo escape($member4 ?? ''); ?>">

            <button type="submit" class="submit-button">Create Team</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
