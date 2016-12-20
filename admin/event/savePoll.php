<?php 
	echo "Entering savePoll.php\n";
	require 'connDB.php';

	// Poll data
	$pollId = $title = $descr = $actDate = $deactDate = "";
        $effDate = $pollType = $dept = $lName = $reason = "";
	
	// Voting data
	$profName = $fName = $lName = $profId = $pollData = $votingInfo = "";
	
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


        if(isset($pollData['lName'])) {
            $lName = $pollData['lName'];
        } else { echo "savePoll.php: professor name not set\n"; }


        if(isset($pollData['dept'])) {
            $dept = $pollData['dept'];
        } else { echo "savePoll.php: department not set\n"; }


        if(isset($pollData['pollId'])) {
            $pollId= $pollData['pollId'];
        } else { echo "savePoll.php: poll ID not set\n"; }

        //echo "\none\n";

	// Update Polls database if pollId exists
	if($pollId > 0) {
                // Update modification history
		$history=":edit:" . "user" . ":" . date("Y-m-d") . ":" . $reason;
		
                // Mysql command to update Poll information
                $cmd = "UPDATE Polls SET title='$title', description='$descr', actDate='$actDate', ";
		$cmd .= "deactDate='$deactDate' , history=CONCAT(history,'$history'), dateEff='$effDate' ";
                $cmd .= "lName='$lName', dept='$dept', pollType='$pollType' WHERE poll_id='$pollId'";	
		//echo "Update Polls cmd: $cmd";
		$result = mysqli_query($conn, $cmd);
			
		if(!$result) { echo "savePoll.php: could not update Polls table\n"; }
		
	} else { // Create new Poll in database
		// Update modification history
                $history="create:" ."user" . ":" . date("Y-m-d") . ":" . $reason; 
		
                // Mysql command to create new Poll
                $cmd = "INSERT INTO Polls(title,description,actDate,deactDate,history,lName,pollType,dept,effDate)";
		$cmd .= " VALUES('$title','$descr','$actDate','$deactDate','$history','$lName','$pollType','$dept','$effDate')";
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
                    } else { echo "savePoll.php: could not fetch poll ID\n"; }
                } else { echo "savePoll.php: could not create new Poll\n"; } 
	}
        
        if($votingInfo) {
            // votingInfo = { "profName" => "comment" } 
            $profNames = array_keys($votingInfo);
            
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
                    
                    // Check if the previous participants are still in the current vote 
                    // (may have been removed if admin edits a saved poll)
                    foreach ($idList as $id) {
                        $getNameCmd = "SELECT fName, lName FROM Users WHERE user_id='$id'";
                        $result = mysqli_query($conn, $getNameCmd);

                        if($result->num_rows > 0) {
                            $removeList = []
                            $tmpProfNames = array_map(strtolower,$profNames);

                            while($row = $result->fetch_assoc()) {
                                $name = $row['fName'];
                                $name .= " ".$row['lName'];
                                $name = strtolower($name);
                                
                                // Add user id to removeList if the professor name is not found
                                // in the most current poll data
                                if(in_array($name,$tmpProfNames) == false) {
                                    $removeList[] = $id;
                                }
                            }
                        }
                        
                        // Remove professors from current poll
                        foreach($removeList as $id) {
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
                $name = explode(' ',$name);
                $fName = $name[0];
                $lName = $name[1];
                
                $selectCmd = "SELECT user_id FROM Users WHERE fName='$fName' and lName='$lName'";
                $result = mysqli_query($conn,$selectCmd);
                
                if($result) {
                    $row = $result->fetch_assoc();
                    $id = $row['user_id'];
                    
                    if(!$id) {
                        echo "savePoll.php: error getting user_id";
                    }

                    $cmt = $votingData[$name];
                    
                    $addToPollCmd = "INSERT INTO Voters(user_id, poll_id, comment, voteFlag) ";
                    $addToPollCmd .= "VALUES('$id','$pollId','$cmt','0')";
                    $result = mysqli_query($conn,$addToPollCmd);
                    
                    if(!$result) {
                        echo "savePoll.php: could not insert user_id='$id'\n";
                    }

                } else { echo "savePoll.php: could not get user_id for user='$name'\n"; };
            }
            
        }
        /*if(isset($pollId)) {
		$profIds = array();
		$cmd="Select user_id from Voters where poll_id='$pollId'";
		$result=mysqli_query($conn,$cmd);
		while($row=$result->fetch_assoc()) {
			array_push($profIds, $row["user_id"]);
		}
	}	
	if($votingInfo) {
		$keys = array_keys($votingInfo);
		for($x = 0; $x < sizeof($keys); ++$x) {
			if($keys[$x] != '0') {
				// Get first name and last name of professor
				$profName = $keys[$x];
				$profNamePieces = explode(" ",$keys[$x]); 
				
                                $fName = $profNamePieces[0];
				$lName = $profNamePieces[1];

				// check if professor is already voting in the current poll
				$cmd = "SELECT user_id from Users WHERE fName='$fName' AND lName='$lName'";
				//echo "cmd: $cmd"; 

				$result = mysqli_query($conn, $cmd);
				if($row = $result->fetch_assoc()) {
					$profId = $row["user_id"];
					//Keep track of which profIds need to be deleted from Votes 
					$profIds = array_diff($profIds,array($profId));
					// Execute cmd, save result, store cmt
					//echo "profName: $profName";
					//echo "profId: $profId";
					//echo "profId: $profId";
					
					$cmt = $votingInfo[$profName];
					//echo "user: $profName cmt: $cmt";
					$cmd = "SELECT * FROM Voters WHERE user_id='$profId' AND poll_id='$pollId'";
					$result = mysqli_query($conn,$cmd);
					$row = $result->fetch_assoc();

					if($row) {
						$cmd = "UPDATE Voters set comment='$cmt' WHERE user_id='$profId' AND poll_id='$pollId'";
						$result = mysqli_query($conn, $cmd);
						
						if(!$result) {
				                    echo "savePoll.php: could not Update cmt for $profName";
						}

					} else {
						echo "user_id: " . $profId . "\n";
						echo "comment: " . $cmt . "\n";
						$cmd = "INSERT INTO Voters(user_id,poll_id,comment,voteFlag) VALUES ('$profId','$newPollId','$cmt',0)";
						$result = mysqli_query($conn,$cmd);
						
						if(!$result) {
						echo "savePoll.php: could not Insert cmt for $profName";
						}
					}
				}
			}
		}
		//Deletes a removed participating prof from Votes 
		if(!empty($profIds)) {
			//var_dump($profIds);	
			$cmd="Delete from Voters where poll_id=$pollId AND user_id IN ('".join("','", $profIds)."')";
			$result=mysqli_query($conn, $cmd);	
		}
	}
        */
?>
