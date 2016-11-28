<!-- Server connection to database= "Professors" begins -->
<?php
        # Setup variables necessary to connect to database
        $serverName = "localhost";
        $userName = "root";
        $pwd = "Computer_Science99";
        $db = "Voting";
        $resultsAvailable = false;

        # Establish connection with db (using setting from variables above)
        $conn = new mysqli($serverName, $userName, $pwd, $db);

        # Check connection to db
        if($conn->connect_error) {
                echo "Connection error: " . $conn->connect_error . "<br>";
        }

        # Select first and last name of professor as well as the professor's title
        #$selectCmd = "SELECT fName, lName, title FROM Professors";
        $selectCmd = "SELECT prof_id, fName, lName, title FROM Professors";

        # Execute command
        $result = $conn->query($selectCmd);


?>

