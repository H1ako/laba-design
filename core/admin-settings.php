<?php
set_time_limit(20);

// Socials and Main Settings
$SITE_URL = 'http://zovisland.ru';
$PROJECT_ROOT = '/zovisland_ru/public_html';
$DEV_URL_PART = '';

$DATABASE_SERVERNAME = '127.0.0.1';
$DATABASE_USERNAME = 'h1ako_zovisland';
$DATABASE_PASSWORD = '&25Qw^67@890';
$DATABASE_NAME = 'h1ako_zovisland';
$DATABASE_PORT = 3308;


// function connectDb()
// {
//     global $DATABASE_SERVERNAME, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME;

//     $conn = mysqli_connect($DATABASE_SERVERNAME, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);
//     $conn->set_charset("utf8mb4");

//     return $conn;
// }

// function disconnectDb($conn)
// {
//     mysqli_close($conn);
// }
