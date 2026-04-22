<?php
$server = "localhost"; 
$username = "root";
$password = "";  //macbook gebruikers vullen bij wachtwoord "root" in.
$db = "epsteinislandescapers"; //pas dit aan indien de naam van jullie database anders is

$db_connection = null;
$db_error = '';

// Try configured database first and a common casing variant as fallback.
$databaseCandidates = array_values(array_unique([$db, 'EpsteinIslandEscapers']));

foreach ($databaseCandidates as $databaseName) {
  try {
    $db_connection = new PDO("mysql:host=$server;dbname=$databaseName;charset=utf8mb4", $username, $password);
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db_error = '';
    break;
  } catch (PDOException $e) {
    $db_error = $e->getMessage();
  }
}