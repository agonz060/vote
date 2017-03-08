<?php 
    session_start();    

    require_once '../event/connDB.php';
    // Vote and poll variables
    $voteData = $pollData = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //print_r($_POST);
        if(!empty($_POST['voteData'])) {
            $voteData = $_POST['voteData'];
        } else { echo "voteData was not provided"; return; }

        if(!empty($_POST['pollData'])) {
            $pollData = $_POST['pollData'];
        } else { echo "pollData was not provided"; return; }

        //print_r($voteData); print_r($pollData);
        beginSubmission($voteData,$pollData);
    }

    function beginSubmission(&$v,&$p) {
        // Titles
        $ASST = "Assistant Professor";
        $ASSOC = "Associate Professor";
        $FULL = "Full Professor";
        // Poll types
        $MERIT = "Merit";
        $PROMOTION = "Promotion";
        $REAPPOINTMENT = "Reappointment";
        $FIFTH_YEAR_REVIEW = "Fifth Year Review";
        $FIFTH_YEAR_APPRAISAL = "Fifth Year Appraisal";
        // Variables
        $title = $pollType = "";

        if(!empty($_SESSION['title'])) {
            $title = $_SESSION['title'];
        }
        if(!empty($p['pollType'])) {
            $pollType = $p['pollType'];
        }

        if($title == $ASST) {
            if($pollType == $MERIT) {
                meritSubmit($v,$p);
            } else {
                assistantSubmit($v,$p);
            }
        } else if($title == $ASSOC || $title == $FULL) {
            if($pollType == $PROMOTION) {
                associatePromoSubmit($v,$p);
            } else if($pollType == $REAPPOINTMENT) {
                reappointmentSubmit($v,$p);
            } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                fifthYrAppraisalSubmit($v,$p);
            } else if($polltype == $FIFTH_YEAR_REVIEW) {
                fifthYrReviewSubmit($v,$p);
            }
        }
    }


    function assistantSubmit(&$v,&$p) {
        $error = updateAssistantTable($v,$p);
        if($error) {
            $errorMsg = "Something went wrong while updating Assistant_Data table";
            echo $errorMsg;
        } else { 
            updateVotersTable($p);
        }
    }
    function meritSubmit(&$v,&$p) {
        $error = updateMeritTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        }
    }

    function reappointmentSubmit(&$v,&$p) {
        $error = updateReappointmentTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        }
    }

    function fifthYrReviewSubmit(&$v,&$p) {
        $error = updateFifthYrReviewTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        }
    }

    function fifthYrAppraisalSubmit(&$v,&$p) {
        $error = updateFifthYrAppraisalTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        } 
    }

    function associatePromoSubmit(&$v,&$p) {
        $error = updateAssociatePromoTable($v,$p);
        /*if(!$error) {
            updateVotersTable($p);
        }
        */ 
    }
    
    function updateMeritTable($v,&$p) {
        global $conn;
        $poll_id = $user_id = $deactDate = $vote = "";
        $voteCmt = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['vote'])) {
            $vote = $v['vote'];
        }
        if(!empty($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Merit_Data";
                $INSERTCMD .= "(poll_id,user_id,vote,voteCmt)";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$vote','$voteCmt')";

                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute: $INSERTCMD while in";
                    $errorMsg .= " updateReappointmentTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Merit_Data table
            } 
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function updateReappointmentTable($v,&$p) {
        global $conn;
        $poll_id = $user_id = $deactDate = $vote = "";
        $voteCmt = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['vote'])) {
            $vote = $v['vote'];
        }
        if(!empty($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Reappointment_Data";
                $INSERTCMD .= "(poll_id,user_id,vote,voteCmt)";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$vote','$voteCmt')";

                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute: $INSERTCMD while in";
                    $errorMsg .= " updateReappointmentTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Reappointment_Data table
            } 
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function updateFifthYrReviewTable($v,&$p) {
        global $conn;
        $poll_id = $user_id = $deactDate = $vote = "";
        $voteCmt = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['vote'])) {
            $vote = $v['vote'];
        }
        if(!empty($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Fifth_Year_Review_Data";
                $INSERTCMD .= "(poll_id,user_id,vote,voteCmt)";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$vote','$voteCmt')";

                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute: $INSERTCMD while in";
                    $errorMsg .= " updateFifthYrReviewTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Fifth_Year_Review_Data table
            } 
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function updateFifthYrAppraisalTable($v,&$p) {
        //print_r($v); print_r($p);
        global $conn;
        $poll_id = $user_id = $deactDate = $vote = "";
        $teachingCmt = $researchCmt = $pubServiceCmt = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['vote'])) {
            $vote = $v['vote'];
        }
        if(!empty($v['teachingCmts'])) {
            $teachingCmts = $v['teachingCmts'];
            $teachingCmts = mysqli_real_escape_string($conn,$teachingCmts);
        }
        if(!empty($v['researchCmts'])) {
            $researchCmts = $v['researchCmts'];
            $researchCmts = mysqli_real_escape_string($conn,$researchCmts);
        }
        if(!empty($v['pubServiceCmts'])) {
            $pubServiceCmts = $v['pubServiceCmts'];
            $pubServiceCmts = mysqli_real_escape_string($conn,$pubServiceCmts);
        }

        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Fifth_Year_Appraisal_Data";
                $INSERTCMD .= "(poll_id,user_id,vote,teachingCmts,researchCmts,pubServiceCmts)";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$vote','$teachingCmts','$researchCmts','$pubServiceCmts')";

                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute: $INSERTCMD while in";
                    $errorMsg .= " updateFifthYrAppraisalTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Fifth_Year_Appraisal_Data table
            } 
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function updateAssociatePromoTable($v,&$p) {
        // Testing
        print_r($v);
        // Function variables
        $ACTION_INFO_ARRAY = "actionInfoArray";
        global $conn;
        $poll_id = $user_id = $fromLevel = $toLevel = "";
        $vote = $voteCmt = $deactDate = $numActions = "";
        $actionInfoArray = "";
        $numActions = $insertCount = 0;
        // Extract user session data
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        // Extract Poll data
        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!empty($p['numActions'])) {
            $numActions = $p['numActions'];
        }
        // Extract vote data
        //$actionInfoArray = json_decode($v[$ACTION_INFO_ARRAY]);
        //print_r($actionInfoArray);

        // Insert all actions into table, if poll is not expired and 
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $test = [];
                while($insertCount < $numActions) {
                    $index = $insertCount;
                    $fromLevel = $action['fromLevel'];
                    $toLEvel = $action['toLevel'];
                    $vote = $action['vote'];
                    $voteCmt = $action['voteCmt'];
                    $actionNum = $action['actionNum'];
                    
                    $INSERTCMD = "INSERT INTO Associate_Promotion_Data(poll_id,";
                    $INSERTCMD .= "user_id,fromLevel,toLevel,vote,voteCmt,actionNum) ";
                    $INSERTCMD .= "VALUES('$poll_id','$user_id','$fromLevel',";
                    $INSERTCMD .= "'$toLevel','$vote','$voteCmt','$actionNum')";
                    $result = mysqli_query($conn,$INSERTCMD);
                    if(!$result) { // Error executing $INSERTCMD
                        $errorMsg = "Could not execute INSERT_CMD: $INSERTCMD while in";
                        $errorMsg .= " updateAssociatePromoTable(...)";
                        echo $errorMsg;
                        return 1; // indicates error has occurred
                    } else { return 0; } // End of successful insert into Assistant_Data table
                    */$insertCount += 1;
                } 
                //print_r($test);
            }
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }


    function updateAssistantTable($v,&$p) {
        global $conn;
        $poll_id = $user_id = $cmt = $deactDate = "";
        //print_r($v);print_r($p);
        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        } 
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['comment'])) {
            $cmt = $v['comment'];
        } else { echo "A comment is required to vote. Please enter comment.";
            return 1; // Error 
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Assistant_Data(poll_id,user_id,voteCmt) ";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$cmt')";
                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute INSERT_CMD: $INSERTCMD while in";
                    $errorMsg .= " updateAssistantTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Assistant_Data table
            } else { // Error poll has expired
                $errorMsg = "Poll expired before submitting vote.";
                echo $errorMsg;
                return 1; // Indicates an error has occurred
            }
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }
    function dataExists($p) {
        global $conn;
        $poll_id = $user_id = $cmt = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        } 
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        $CHECKEXISTINGDATACMD = "SELECT voteFlag FROM Voters ";
        $CHECKEXISTINGDATACMD .= "WHERE poll_id=$poll_id AND user_id=$user_id";

        $result = mysqli_query($conn,$CHECKEXISTINGDATACMD);
        if($result) {
            $row = $result->fetch_assoc();
            $voteFlag = $row['voteFlag'];
            if($voteFlag == 0) {
                return false;
            } else if($voteFlag == 1) { 
                return true; 
            }
        } else { // Error executing mysqli command
            $errorMsg = "Couldn't execute CHECK_EXISTING_DATA_CMD: ";
            $errorMsg .= "$CHECKEXISTINGDATACMD";
            echo $errorMsg;
            return true;
        }
    }

    function updateVotersTable($p) {
        global $conn;
        $poll_id = $user_id = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        } 
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        $UPDATEVOTERSCMD = "UPDATE Voters SET voteFlag=1 WHERE ";
        $UPDATEVOTERSCMD .= "poll_id=$poll_id AND user_id=$user_id";
        //echo $UPDATEVOTERSCMD."<br>";

        $result = mysqli_query($conn,$UPDATEVOTERSCMD);
        if(!$result) { // Error executing mysqli command
            $errorMsg = "Could not execute UPDATE_VOTER_CMD: $UPDATEVOTERSCMD";
            echo $errorMsg;
        } 
    }
    
    function isPollExpired($d) {
        date_default_timezone_set('America/Los_Angeles');
            $deactDateTime = strtotime($d);
            $currentTime = strtotime("now");

            if($currentTime < $deactDateTime) {
                // There is still time to vote
                return false;
            } else { return true; }
    }
?>
