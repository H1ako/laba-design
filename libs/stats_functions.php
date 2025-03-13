<?php

function getInstallationsNumber()
{
    try {

        $conn = connectDb();

        $installationsNumber = mysqli_query($conn, "SELECT * FROM stats WHERE `key` = 'installationsNumber'");

        if (mysqli_num_rows($installationsNumber) > 0) {
            $row = mysqli_fetch_assoc($installationsNumber);
            disconnectDb($conn);
            return $row['value'];
        }

        disconnectDb($conn);

        return 0;
    } catch (Exception $e) {
        return 55;
    }
}


function increaseInstallationsNumber()
{
    try {

        $conn = connectDb();
        if (!$conn) {
            return false;
        }

        $installationsNumber = mysqli_query($conn, "SELECT * FROM stats WHERE `key` = 'installationsNumber'");

        $query = "INSERT INTO stats (`key`, `value`) VALUES ('installationsNumber', 1)";
        if (mysqli_num_rows($installationsNumber) > 0) {
            $row = mysqli_fetch_assoc($installationsNumber);
            $newValue = intval($row['value']) + 1;
            $query = "UPDATE stats SET `value` = '$newValue' WHERE `key` = 'installationsNumber'";
        }

        $result = mysqli_query($conn, $query);

        disconnectDb($conn);

        return $result;
    } catch (Exception $e) {
    }
}


function getGenerationsNumber()
{
    try {

        $conn = connectDb();
        if (!$conn) {
            return 150000;
        }

        $generationsNumber = mysqli_query($conn, "SELECT * FROM stats WHERE `key` = 'generationsNumber'");

        if (mysqli_num_rows($generationsNumber) > 0) {
            $row = mysqli_fetch_assoc($generationsNumber);
            disconnectDb($conn);
            return $row['value'];
        }

        disconnectDb($conn);

        return 0;
    } catch (Exception $e) {
        return 150000;
    }
}


function increaseGenerationsNumber()
{
    try {
        $conn = connectDb();
        if (!$conn) {
            return false;
        }
        $generationsNumber = mysqli_query($conn, "SELECT * FROM stats WHERE `key` = 'generationsNumber'");

        $query = "INSERT INTO stats (`key`, `value`) VALUES ('generationsNumber', 1)";
        if (mysqli_num_rows($generationsNumber) > 0) {
            $row = mysqli_fetch_assoc($generationsNumber);
            $newValue = intval($row['value']) + 1;
            $query = "UPDATE stats SET `value` = '$newValue' WHERE `key` = 'generationsNumber'";
        }

        $result = mysqli_query($conn, $query);

        disconnectDb($conn);
        return $result;
    } catch (Exception $e) {
    }
}

function getStats()
{
    try {
        $conn = connectDb();
        if (!$conn) {
            return false;
        }
        $stats = mysqli_query($conn, "SELECT * FROM stats");

        $result = [];
        while ($row = mysqli_fetch_assoc($stats)) {
            $result[$row['key']] = $row['value'];
        }

        disconnectDb($conn);

        return $result;
    } catch (Exception $e) {
        return [
            'generationsNumber' => 150000,
            'installationsNumber' => 53
        ];
    }
}
