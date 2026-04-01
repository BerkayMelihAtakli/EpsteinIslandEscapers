<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['cult_unlocked'])) {
  header('Location: /EpsteinIslandEscapers/index.php#cult-riddle');
  exit;
}

require_once('../dbcon.php');

try {
  $stmt = $db_connection->query("SELECT * FROM question WHERE roomId = 2");
  $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Databasefout: " . $e->getMessage());
}
?>

