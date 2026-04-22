<?php
$server = 'localhost';
$username = 'root';
$password = '';
$databaseName = 'epsteinislandescapers';

$dbConnection = null;
$dbConnectionError = '';

try {
  $adminConnection = new PDO("mysql:host=$server", $username, $password);
  $adminConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $adminConnection->exec("CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

  $dbConnection = new PDO("mysql:host=$server; dbname=$databaseName;charset=utf8mb4", $username, $password);
  $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  $dbConnectionError = 'Connection failed: ' . $e->getMessage();
}

// Backward-compatible aliases used by existing files.
$db_connection = $dbConnection;
$db_connection_error = $dbConnectionError;
