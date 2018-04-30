<?php
    session_start();
	//print_r($_SESSION);
    require_once '../includes/connDB.php';
    require_once '../includes/functions.php';

    if(empty($_SESSION['user_id'])) {
        echo "submitVote.php: user_id not set.";
        return;
    }

// Vote and poll variables
    $voteData = $pollData = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //print_r($_POST);
        if(isset($_POST['voteData'])) {
            $voteData = $_POST['voteData'];
        } else { echo "voteData was not provided"; return; }

        if(isset($_POST['pollData'])) {
            $pollData = $_POST['pollData'];
        } else { echo "pollData was not provided"; return; }

        //print_r($voteData); print_r($pollData);
        beginSubmission($voteData,$pollData);
    }

    function getUserForm($uTitle,&$pollData) {
        $ASSISTANT = "Assistant Professor";
        $ASSOCIATE = "Associate Professor";
        $FULL = "Full Professor";

        switch($uTitle) {
            case $ASSISTANT:
                return $pollData['assistantForm'];
                break;
            case $ASSOCIATE:
                return $pollData['associateForm'];
                break;
            case $FULL:
                return $pollData['fullForm'];
                break;
            default: // Error
                return -1;
        }
    }

    function beginSubmission(&$v,&$p) {
        // Titles
        $ASST = "Assistant Professor";
        $ASSOC = "Associate Professor";
        $FULL = "Full Professor";
        // Poll types
        $OTHER = "Other";
        $MERIT = "Merit";
        $PROMOTION = "Promotion";
        $REAPPOINTMENT = "Reappointment";
        $FIFTH_YEAR_REVIEW = "Fifth Year Review";
        $FIFTH_YEAR_APPRAISAL = "Fifth Year Appraisal";
        // Forms
        $REGULAR_FORM = 1;
        $ADVISORY_FORM = 2;
        $EVALUATION_FORM = 3;
        // Variables
        $userTitle = $profTitle = $pollType = "";

        if(isset($_SESSION['title'])) {
            $userTitle = $_SESSION['title'];
        }
        if(isset($p['pollType'])) {
            $pollType = $p['pollType'];
        }

        $userForm = getUserForm($userTitle,$p);
        // print "pollType: $pollType userTitle: $userTitle userForm: $userForm";
        if($userForm == $REGULAR_FORM || $userForm == $ADVISORY_FORM) {
            switch($pollType) {
                case $REAPPOINTMENT:
                    reappointmentSubmit($v,$p);
                    break;
                case $PROMOTION:
                    promotionSubmit($v,$p);
                    break;
                case $FIFTH_YEAR_REVIEW:
                    fifthYrReviewSubmit($v,$p);
                    break;
                case $MERIT:
                    meritSubmit($v,$p);
                    break;
                case $FIFTH_YEAR_APPRAISAL:
                    fifthYrAppraisalSubmit($v,$p);
                    break;
                case $OTHER:
                    otherPollSubmit($v,$p);
                    break;
                default:
                    $ERROR_MSG = "submitVote.php: could not locate correct data table";
                    echo "$ERROR_MSG";
            } // End switch
        } else { // $userForm == $EVALUATION_FORM
                evaluationSubmit($v,$p);
        }
    }
    function otherPollSubmit(&$v,&$p) {
        $FALSE = 0;
        $error = updateOtherPollTable($v,$p);
        if($error == $FALSE) {
            updateVotersTable($p);
        }
    }
    function evaluationSubmit(&$v,&$p) {
        //echo "entering confidential evaluation submit";
        $FALSE = 0;
        $error = updateEvaluationTable($v,$p);
        if($error == $FALSE) {
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
    function promotionSubmit(&$v,&$p) {
        $error = updatePromotionTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        }
    }
    function updateMeritTable($v,&$p) {
        global $conn;
        $poll_id = $user_id = $vote = $voteCmt = "";
        $deactDate = $numActions = "";
        $insertCount = 0;
        $actionInsertErrors = array();
        // Extract user session data
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        // Extract Poll data
        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($p['numActions'])) {
            $numActions = $p['numActions'];
        }
        // Insert all actions into table, if poll is not expired and
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                while($insertCount < $numActions) {
                    // Extract action array from vote data ($v)
                    $index = $insertCount;
                    $action = $v[$index];
                    $actionNum = $index + 1;
                    // Extract action data
                    $vote = $action['vote'];
                    $voteCmt = $action['voteCmt'];
                    $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
                    // Insert action data into database
                    $INSERTCMD = "INSERT INTO Merits(poll_id,";
                    $INSERTCMD .= "user_id,vote, voteCmt, action_num) ";
                    $INSERTCMD .= "VALUES($poll_id,$user_id, ";
                    $INSERTCMD .= "$vote,'$voteCmt',$actionNum)";
                    $result = mysqli_query($conn,$INSERTCMD) or die (mysqli_error($conn));
                    if(!$result) { // Error executing $INSERTCMD
                        $actionInsertErrors[] = $actionNum;
                    }
                    $insertCount += 1;
                }
            } else {
                date_default_timezone_set("America/Los_Angeles");
                $currentTime = date("h:i:sa");
                $errorMsg = "Poll has expired. Current time: $currentTIme";
                echo $errorMsg;
                return 1;
            } // End of else if(...)
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

        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(isset($v['vote'])) {
            $vote = $v['vote'];
        }
        if(isset($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Reappointments";
                $INSERTCMD .= "(poll_id,user_id,vote,voteCmt)";
                $INSERTCMD .= "VALUES($poll_id,$user_id,$vote,'$voteCmt')";

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
        $voteCmt = "";
        $poll_id = $user_id = $deactDate = $vote = "";

        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(isset($v['vote'])) {
            $vote = $v['vote'];
        }
        if(isset($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Fifth_Year_Reviews";
                $INSERTCMD .= "(poll_id,user_id,vote,voteCmt)";
                $INSERTCMD .= "VALUES($poll_id,$user_id,$vote,'$voteCmt')";
                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute: $INSERTCMD while in";
                    $errorMsg .= " updateFifthYrReviewTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { //echo "End of successful insert";
			return 0; } // End of successful insert into Fifth_Year_Review_Data table
            } else { // Poll expired
                echo "Poll expired";
                return 1; // error
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

        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(isset($v['vote'])) {
            $vote = $v['vote'];
        }
        if(isset($v['teachingCmts'])) {
            $teachingCmts = $v['teachingCmts'];
            $teachingCmts = mysqli_real_escape_string($conn,$teachingCmts);
        }
        if(isset($v['researchCmts'])) {
            $researchCmts = $v['researchCmts'];
            $researchCmts = mysqli_real_escape_string($conn,$researchCmts);
        }
        if(isset($v['pubServiceCmts'])) {
            $pubServiceCmts = $v['pubServiceCmts'];
            $pubServiceCmts = mysqli_real_escape_string($conn,$pubServiceCmts);
        }

        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Fifth_Year_Appraisals";
                $INSERTCMD .= "(poll_id,user_id,vote,teachingCmts,researchCmts,pubServiceCmts)";
                $INSERTCMD .= "VALUES($poll_id,$user_id,$vote,'$teachingCmts','$researchCmts','$pubServiceCmts')";

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

    function updateOtherPollTable($v,&$p) {
        // Function variables
        global $conn;
        $poll_id = $user_id = $vote = $voteCmt = "";
        $deactDate = $numActions = "";
        $insertCount = 0;
        $actionInsertErrors = array();
        // Extract user session data
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        // Extract Poll data
        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($p['numActions'])) {
            $numActions = $p['numActions'];
        }
        // Insert all actions into table, if poll is not expired and
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                while($insertCount < $numActions) {
                    // Extract action array from vote data ($v)
                    $index = $insertCount;
                    $action = $v[$index];
                    $actionNum = $index + 1;
                    // Extract action data
                    $vote = $action['vote'];
                    $voteCmt = $action['voteCmt'];
                    $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
                    // Insert action data into database
                    $INSERTCMD = "INSERT INTO Other_Polls(poll_id,";
                    $INSERTCMD .= "user_id,vote, voteCmt, action_num) ";
                    $INSERTCMD .= "VALUES($poll_id,$user_id,";
                    $INSERTCMD .= "$vote,'$voteCmt',$actionNum)";
                    $result = mysqli_query($conn,$INSERTCMD) or die (mysqli_error($conn));
                    if(!$result) { // Error executing $INSERTCMD
                        $actionInsertErrors[] = $actionNum;
                    }
                    $insertCount += 1;
                }
            } else {
                date_default_timezone_set("America/Los_Angeles");
                $currentTime = date("h:i:sa");
                $errorMsg = "Poll has expired. Current time: $currentTIme";
                echo $errorMsg;
                return 1;
            } // End of else if(...)
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function updatePromotionTable($v,&$p) {
        // Function variables
        global $conn;
        $poll_id = $user_id = $vote = $voteCmt = "";
        $deactDate = $numActions = "";
        $insertCount = 0;
        $actionInsertErrors = array();
        // Extract user session data
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        // Extract Poll data
        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(isset($p['numActions'])) {
            $numActions = $p['numActions'];
        }
        // Insert all actions into table, if poll is not expired and
        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                while($insertCount < $numActions) {
                    // Extract action array from vote data ($v)
                    $index = $insertCount;
                    $action = $v[$index];
                    $actionNum = $index + 1;
                    // Extract action data
                    $vote = $action['vote'];
                    $voteCmt = $action['voteCmt'];
                    $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
                    // Insert action data into database
                    $INSERTCMD = "INSERT INTO Promotions(poll_id,";
                    $INSERTCMD .= "user_id,vote, voteCmt, action_num) ";
                    $INSERTCMD .= "VALUES($poll_id,$user_id,";
                    $INSERTCMD .= "$vote,'$voteCmt',$actionNum)";
                    $result = mysqli_query($conn,$INSERTCMD) or die (mysqli_error($conn));
                    if(!$result) { // Error executing $INSERTCMD
                        $actionInsertErrors[] = $actionNum;
                    }
                    $insertCount += 1;
                }
            } else {
                date_default_timezone_set("America/Los_Angeles");
                $currentTime = date("h:i:sa");
                $errorMsg = "Poll has expired. Current time: $currentTIme";
                echo $errorMsg;
                return 1;
            } // End of else if(...)
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }


    function updateEvaluationTable($v,&$p) {
        // echo "1";
        global $conn;
        $poll_id = $user_id = $voteCmt = $deactDate = "";
        //print_r($v);print_r($p);
        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(isset($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
            $voteCmt = mysqli_real_escape_string($conn,$voteCmt);
        }
        if(isset($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }
        if(!dataExists($p)) {
            // echo "2";
            if(!isPollExpired($deactDate)) {
                // echo "3";
                $insertCmd = "INSERT INTO confidential_evals(poll_id,user_id,voteCmt) ";
                $insertCmd .= "VALUES($poll_id,$user_id,'$voteCmt')";
                $result = mysqli_query($conn,$insertCmd) or die(mysqli_error($conn));
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute INSERT_CMD: $INSERTCMD while in";
                    $errorMsg .= " updateEvaluationsTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Assistant_Data table
            } else { // Error poll has expired
                $errorMsg = "Poll expired before submitting vote.";
                echo $errorMsg;
                return 1; // Indicates an error has occurred
            } // End of else if([pollExpired])
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

        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        $CHECKEXISTINGDATACMD = "SELECT voteFlag FROM Voters ";
        $CHECKEXISTINGDATACMD .= "WHERE poll_id={$poll_id} AND user_id={$user_id}";

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

        if(isset($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        $UPDATEVOTERSCMD = "UPDATE Voters SET voteFlag=1 WHERE ";
        $UPDATEVOTERSCMD .= "poll_id={$poll_id} AND user_id={$user_id}";
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
