<?php
        # Setup variables necessary to connect to database
        $serverName = "localhost";
        $userName = "root";
        $pwd = "Computer_Science99";
        $db = "Voting";

        # Establish connection with db (using setting from variables above)
        $conn = new mysqli($serverName, $userName, $pwd, $db);

        # Check connection to db
        if($conn->connect_error) {
                echo "Connection error: " . $conn->connect_error . "<br>";
        }
?>
