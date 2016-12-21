<?php 
	//echo "Entering savePoll.php\n";
	require 'connDB.php';

	// Poll data
	$pollId = $title = $descr = $actDate = $deactDate = "";
    $effDate = $pollType = $dept = $name = $reason = "";
	
	// Voting data
	$profName = $fName = $lName = $profId = $pollData = $votingInfo = "";
    $voters = [];
	
	//Set Timezone
	date_default_timezone_set('America/Los_Angeles');

	// Check if data is set before accessing
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["pollData"])) {
			$pollData = $_POST["pollData"];
			//echo "pollData: "; print_r($pollData);
			//print_r(array_keys($pollData));
			//echo "pollId: " + $pollData['pollId'];
		} else { echo "savePoll.php: error pollData not set\n"; }

		if(isset($_POST["votingInfo"])) {
			$votingInfo = $_POST["votingInfo"];
			//echo " votingInfo: "; print_r($votingInfo);
		} else { echo "savePoll.php: error votingInfo not set"; }
	        
            if(isset($_POST["reason"])) {
                $reason = $_POST["reason"];
            } else { echo "savePoll.php: error reason not set"; }
        }
    
        if(isset($pollData['title'])) {
            $title = $pollData['title'];
        } else { echo "savePoll.php: title not set\n"; }

        if(isset($pollData['descr'])) {
            $descr = $pollData['descr'];
        } else { echo "savePoll.php: description not set\n"; }


        if(isset($pollData['actDate'])) {
            $actDate = $pollData['actDate'];
        } else { echo "savePoll.php: activation date not set\n"; }


        if(isset($pollData['deactDate'])) {
            $deactDate = $pollData['deactDate'];
        } else { echo "savePoll.php: deactivation date not set\n"; }

        if($pollData['effDate']) {
            $effDate = $pollData['effDate'];
        }

        if(isset($pollData['pollType'])) {
            $pollType = $pollData['pollType'];
        } else { echo "savePoll.php: poll type not set\n"; }


        if(isset($pollData['name'])) {
            $name = $pollData['name'];
        } else { echo "savePoll.php: professor name not set\n"; }


        if(isset($pollData['dept'])) {
            $dept = $pollData['dept'];
        } else { echo "savePoll.php: department not set\n"; }


        if(isset($pollData['pollId'])) {
            $pollId= $pollData['pollId'];
        } else { echo "savePoll.php: poll ID not set\n"; }

        //echo "\none\n";

	// Update Polls database if pollId exists
    //echo "pollId set to 30\n";
    //$pollId = 36;
	
    if($pollId > 0) {
        //echo 'Updating existing poll id: '.$pollId."\n";
        // Update modification history
		$history=":edit:" . "user" . ":" . date("Y-m-d") . ":" . $reason;
		
        // Mysql command to update Poll information
        $cmd = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
		$cmd .= "deactDate='$deactDate' , history=CONCAT(history,'$history'), effDate='$effDate',";
        $cmd .= " name='$name', dept='$dept', pollType='$pollType' WHERE poll_id='$pollId'";	
        //echo "updating cmd: $cmd\n";
		//echo "Update Polls cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
			
		if(!$result) { echo "savePoll.php: could not update Polls table\n"; }
		
	} else { // Create new Poll in database
        //echo "Poll id not found. Creating new poll\n";
		// Update modification history
        $history="create:" ."user" . ":" . date("Y-m-d") . ":" . $reason; 

        // Mysql command to create new Poll
        $cmd = "INSERT INTO Polls(title,description,actDate,deactDate,history,name,pollType,dept,effDate)";
		$cmd .= " VALUES('$title','$descr','$actDate','$deactDate','$history','$name','$pollType','$dept','$effDate')";
	    //echo "$cmd";

        $result = mysqli_query($conn,$cmd);	
		// Create new poll
		if($result) {
            // Order table by id from highest ID number to the lowest ID number and  
            // limit the result to the first ID number to get the most recently added poll
            $getIDCmd = "SELECT * FROM Polls ORDER BY poll_id DESC LIMIT 1";
            $result = mysqli_query($conn, $getIDCmd);
            
            if($result) {
                $row = $result->fetch_assoc();

                $pollId = $row['poll_id'];
                //echo 'New pollId: '.$pollId."\n";
                //echo "Finished updating Polls table\n";
            } else { echo "savePoll.php: could not fetch poll ID\n"; }
        } else { echo "savePoll.php: could not create new Poll\n"; } 
	}
        
    if($votingInfo) {
        //echo "Updating Voters table\n";
        // votingInfo = { "profName" => "comment" } 
        $profNames = array_keys($votingInfo);
        //echo 'profNames: '; print_r($profNames);

        // Get all previously saved participants of the current vote
        $getProfsCmd = "SELECT user_id FROM Voters WHERE poll_id='$pollId'";
        $result = mysqli_query($conn, $getProfsCmd);

        if($result) {
           if($result->num_rows > 0) {
                // Keep list of professor ID's 
                $idList = [];

                while($row = $result->fetch_assoc()) {
                    $idList[] = $row['user_id'];
                }
                //echo 'Ids assoc with current poll: '.$pollId." : "; print_r($idList);
                $tmpProfNames = array_map('strtolower',$profNames);
                //echo 'profNames converted to lower: ';print_r($tmpProfNames);

                // Check if the previous participants are still in the current vote 
                // (may have been removed if admin edits a saved poll)
                foreach ($idList as $id) {
                    $getNameCmd = "SELECT fName, lName FROM Users WHERE user_id='$id'";
                    $result = mysqli_query($conn, $getNameCmd);

                    if($result->num_rows > 0) {
                        $removeList = [];
                       
                        while($row = $result->fetch_assoc()) {
                            $name = $row['fName'];
                            $name .= " ".$row['lName'];
                            $name = strtolower($name);
                            //echo "profId: ".$id." name: ".$name." Searching through current prof list\n";

                            // Add user id to removeList if the professor name is not found
                            // in the most current poll data
                            if(in_array($name,$tmpProfNames) == false) {
                                //echo 'name: '.$name." not found in currently voting professors\n";
                                $removeList[] = $id;
                            } else { 
                                // if user is found then user remains in the current vote
                                $voters[] = $id; }
                        }
                    }
                    
                    // Remove professors from current poll
                    foreach($removeList as $id) {
                        //echo 'removing from vote id: '.$id."\n";
                        $delCmd = "DELETE FROM Voters WHERE user_id='$id'";
                        $result = mysqli_query($conn, $delCmd);

                        if(!$result) {
                            echo "savePoll.php: could not delete user_id='$id'\n";
                        }
                    }
                }
           }
        } else { echo "savePoll.php: error executing getProfsCmd\n"; }
        
        // Add professors to current poll by inserting prof's 
        // id and comment into the Voters table
        foreach($profNames as $name) {
            // Get user id of each professor 
            //echo 'Name:'.$name."\n" ;
            $nameSplit = explode(' ',$name);
            $fName = $nameSplit[0];
            $lName = $nameSplit[1];
            
            $selectCmd = "SELECT user_id FROM Users WHERE fName='$fName' and lName='$lName'";
            $result = mysqli_query($conn,$selectCmd);
            
            if($result) {
                $row = $result->fetch_assoc();
                $id = $row['user_id'];
                
                if(!$id) { echo "savePoll.php: error getting user_id"; }

                $cmt = $votingInfo[$name];
                
                // Voter already part of current vote then UPDATE voters data 
                if(in_array($id, $voters)) {
                    //echo 'update voter: '.$id." comment\n";
                    $updateVoter = "UPDATE Voters SET comment='$cmt' WHERE user_id='$id' AND poll_id='$pollId'";
                } else { // Voter is new to poll, i.e INSERT voter into poll
                    //echo "inserting new voter: $id into Voters table\n";
                    //echo "voters: ";print_r($voters);

                    $addToPollCmd = "INSERT INTO Voters(user_id, poll_id, comment, voteFlag) ";
                    $addToPollCmd .= "VALUES('$id','$pollId','$cmt','0')";
                    $result = mysqli_query($conn,$addToPollCmd);
                    
                    if(!$result) { echo "savePoll.php: could not insert user_id='$id'\n"; }
                }

            } else { echo "savePoll.php: could not get user_id for user='$name'\n"; };
        }    
    }
?>
