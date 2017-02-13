<?php 
    session_start();    
    require_once '../event/connDB.php';
    // Vote and poll variables
    $voteData = $pollData = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $MERRIT = "Merrit";
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
            assistantSubmit($v,$p);
        } else if($title == $ASSOC || $title == $FULL) {
            if($pollType == $PROMOTION) {
                #associatePromoSubmit($v,$p);
            } else if($pollType == $REAPPOINTMENT) {
                //reappointmentSubmit($v,$p);
            } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                fifthYrAppraisalSubmit($v,$p);
            } else if($polltype == $FIFTH_YEAR_REVIEW) {
                //fifthYrReviewSubmit($v,$p);
            }
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
        if(!$error) {
            updateVotersTable($p);
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
        global $conn;
        $poll_id = $user_id = $fromLevel = $toLevel = "";
        $vote = $voteCmt = $deactDate = "";

        if(!empty($p['poll_id'])) {
            $poll_id = $p['poll_id'];
        }
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($v['fromLevel'])) {
            $fromLevel = $v['fromLevel'];
        }
        if(!empty($v['toLevel'])) {
            $toLevel = $v['toLevel'];
        }
        if(!empty($v['vote'])) {
            $vote = $v['vote'];
        }
        if(!empty($v['voteCmt'])) {
            $voteCmt = $v['voteCmt'];
        }
        if(!empty($p['deactDate'])) {
            $deactDate = $p['deactDate'];
        }

        if(!dataExists($p)) {
            if(!isPollExpired($deactDate)) {
                $INSERTCMD = "INSERT INTO Associate_Promotion_Data(poll_id,";
                $INSERTCMD .= "user_id,fromLevel,toLevel,vote,voteCmt) ";
                $INSERTCMD .= "VALUES('$poll_id','$user_id','$fromLevel',";
                $INSERTCMD .= "'$toLevel','$vote','$voteCmt')";
                $result = mysqli_query($conn,$INSERTCMD);
                if(!$result) { // Error executing $INSERTCMD
                    $errorMsg = "Could not execute INSERT_CMD: $INSERTCMD while in";
                    $errorMsg .= " updateAssociatePromoTable(...)";
                    echo $errorMsg;
                    return 1; // indicates error has occurred
                } else { return 0; } // End of successful insert into Assistant_Data table
            } 
        } else { // Error duplicate entry, only one submission allowed
            $errorMsg = "Dual submission encountered. Each participating voter";
            $errorMsg .= " may cast a vote once per poll.";
            echo $errorMsg;
            return 1; // Indicates error has occured
        }
    }

    function assistantSubmit(&$v,&$p) {
        $error = updateAssistantTable($v,$p);
        if(!$error) {
            updateVotersTable($p);
        } else { // Something went wrong while updating assistant data table
            $errorMsg = "Something went wrong while updating Assistant_Data table";
            echo $errorMsg;
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