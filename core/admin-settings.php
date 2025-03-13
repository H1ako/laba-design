<?php
set_time_limit(20);

// Socials and Main Settings
$SITE_URL = 'http://localhost/laba_dis';
$PROJECT_ROOT = 'C:/php_projects/laba_dis';
$DEV_URL_PART = '/laba_dis';

$DATABASE_SERVERNAME = 'localhost';
$DATABASE_USERNAME = 'root';
$DATABASE_PASSWORD = 'root';
$DATABASE_NAME = 'bd_20_laba';


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
