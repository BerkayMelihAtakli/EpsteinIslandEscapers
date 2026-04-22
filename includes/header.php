<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escape Room</title>
    <link rel="icon" type="image/png" href="/EpsteinIslandEscapers/assets/logo.png">
    <link rel="shortcut icon" type="image/png" href="/EpsteinIslandEscapers/assets/logo.png">
    <link rel="stylesheet" href="/EpsteinIslandEscapers/css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
</head>

<body>