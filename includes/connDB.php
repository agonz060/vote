<?php
        # Setup variables necessary to connect to database
        $serverName = "localhost";
        $userName = "root";
        $pwd = "comp_sci99";
        // $pwd = "sqlADMpwd4";
        $db = "Voting";

        # Establish connection with db (using setting from variables above)
        $conn = new mysqli($serverName, $userName, $pwd, $db);

        # Check connection to db
        if($conn->connect_error) {
                echo "Connection error: " . $conn->connect_error . "<br>";
        }

