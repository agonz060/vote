<?php 
    session_start();
    
    // var_dump($_SESSION);

    if(idleTimeLimitReached()) {
        signOut();
    } else if(isPollExpired()) {
        cancelVote();
    } else { updateLastActivity(); }

    function idleTimeLimitReached() {
        if(!(empty($_SESSION['LAST_ACTIVITY']))) {
            if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
                if(isSessionExpired()) {
                    return 1;
                } else { return 0; }
            } else { // Error must have occurred
                    return 1; }
        } else { // Error must have occurred 
            return 1; }
    } // End of isValidSession() 

    // Check for expired activity
    function isSessionExpired() {
        $lastActivity = $_SESSION['LAST_ACTIVITY'];
        $timeOut = $_SESSION['IDLE_TIME_LIMIT'];
        // Check if session has been active longer than IDLE_TIME_LIMIT
        if(time() - $lastActivity >= $timeOut) {
            return true;
        } else { false; }   
    }// End of isSesssionExpired()

    function updateLastActivity() {
        $_SESSION['LAST_ACTIVITY'] = time();
        return;
    }

    function saveSessionVars() {
        session_write_close();
        return;
    }

    function unsetSessionVariables() {
        // Session variables accessed
        $CMT = "cmt";
        $POLL_ID = "poll_id";
        $POLL_TYPE = "pollType";
        $PROF_NAME = "profName";
        $DEACT_DATE = "deactDate";

        unset($GLOBALS['_SESSION'][$CMT]);
        unset($GLOBALS['_SESSION'][$POLL_ID]);
        unset($GLOBALS['_SESSION'][$POLL_TYPE]);
        unset($GLOBALS['_SESSION'][$PROF_NAME]);
        unset($GLOBALS['_SESSION'][$DEACT_DATE]);
    }

    function updateAndSave() {
        unsetSessionVariables();
        updateLastActivity();
        saveSessionVars();
    }

    function signOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();

        saveSessionVars();
        redirectToLogIn();
    }

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>";
        echo $jsRedirect;
        return;
    }

// End session verification  
?>
<?php
    require_once '../event/connDB.php';

    // Poll data
    $name = $deactDate = $pollType = "";
    $alertMsg = $dataErrorMsg = "";

    // Get all posted poll data
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // Security and poll checks
        if(idleTimeLimitReached()) {
            signOut();
        } else if(isPollExpired()) {
            cancelVote();
        }

        if(!empty($_POST['action'])) {
            $SUBMIT = "Submit";
            $CANCEL = "Cancel";

            $cmt = "";
            $action = $_POST['action'];

            if($action == $SUBMIT) {
                if(!empty($_POST['comment'])) {
                    $_SESSION['cmt'] = cleanInput($_POST['comment']);
                    submitVote();
                } else {
                    $dataErrorMsg = "<font color='red'>* Comment required</font>";
                }
            } else if($action == $CANCEL) {
                cancelVote();
            }
             
        } else { // Get poll data posted by /user/edit.php 
            //echo "Loading poll data<br>";
            if(!empty($_POST['poll_id'])) {
                //echo "pollId set\n";
                $_SESSION['poll_id'] = cleanInput($_POST['poll_id']);
            } else if(empty($_SESSION['poll_id'])) {
                $alertMsg = "asst.php: error getting poll_id\n";
                errorGettingPollData();
            }

            if(!empty($_POST['pollType'])) {
                //echo "pollType set\n";
                $_SESSION['pollType'] = cleanInput($_POST['pollType']);
            } else if(empty($_SESSION['pollType'])) {
                $alertMsg = "asst.php: error getting pollType";
                errorGettingPollData();
            }
        
            if(!empty($_POST['profName'])) {
                //echo "profName set\n";
                $_SESSION['profName'] = cleanInput($_POST['profName']);
            } else if(empty($_SESSION['profName'])) { 
                $alertMsg = "asst.php: errror getting profName";
                errorGettingPollData();
            }

            if(!empty($_POST['deactDate'])) {
                $_SESSION['deactDate'] = cleanInput($_POST['deactDate']);
            } else if(empty($_SESSION['deactDate'])) {
                $alertMsg = "asst.php: error getting deactDate";
                errorGettingPollData();
            }
            //print_r($_SESSION);
        } // End !empty($_POST['action']) else block
        updateLastActivity();
    } // End of $_SERVER['REQUEST_METHOD']

    function isPollExpired() {
        date_default_timezone_set('America/Los_Angeles');
        if(!empty($_SESSION['deactDate'])) {
            $deactDate = $_SESSION['deactDate'];
            $deactDateTime = strtotime($deactDate);
            $currentTime = strtotime("now");

            if($currentTime < $deactDateTime) {
                // There is still time to vote
                return false;
            } else { return true; }
        }
    }

    function submitVote() {
        updateAssistantTable();
        if(updateVotersTable()) {
            $alertMsg = "Vote submitted!";
            alertMsg($alertMsg);
            updateAndSave();
            redirectToEditPage();
        } else { // Error while updating Voters table
            $alertMsg = 'asst.php: error - could not execute $UPDATEVOTERSCMD in function updateVotersTable()'; 
            alertMsg($alertMsg);
            updateAndSave();
            redirectToEditPage();
        }
    }

    function updateVotersTable() {
        global $conn;
        $poll_id = $user_id = "";

        if(!empty($_SESSION['poll_id'])) {
            $poll_id = $_SESSION['poll_id'];
        } 
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        $UPDATEVOTERSCMD = "UPDATE Voters SET voteFlag=1 WHERE ";
        $UPDATEVOTERSCMD .= "poll_id=$poll_id AND user_id=$user_id";
        //echo $UPDATEVOTERSCMD."<br>";

        $result = mysqli_query($conn,$UPDATEVOTERSCMD);
        if(!$result) { // Error executing mysqli command
            return false;
        } else { return true; }
    }

    function updateAssistantTable() {
        global $conn;
        $poll_id = $user_id = $cmt = "";

        if(!empty($_SESSION['poll_id'])) {
            $poll_id = $_SESSION['poll_id'];
        } 
        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
        if(!empty($_SESSION['cmt'])) {
            $cmt = $_SESSION['cmt'];
        }

        if(!dataExists()) {
            $INSERTCMD = "INSERT INTO Assistant_Data(poll_id,user_id,voteCmt) ";
            $INSERTCMD .= "VALUES('$poll_id','$user_id','$cmt')";

            $result = mysqli_query($conn,$INSERTCMD);
            if(!$result) { // Error executing $INSERTCMD
                $alertMsg = 'asst.php: error - executing $INSERTCMD';
                alertMsg($alertMsg);
                updateAndSave();
                redirectToEditPage();
            }
            return;// End of successful insert into Assistant_Data table
        } else { // Error duplicate entry, only one submission allowed
            $alertMsg = "asst.php: error - voting data for this poll and from this user already exists!";
            alertMsg($alertMsg);
            updateAndSave();
            redirectToEditPage();
        }
    }

    function dataExists() {
        global $conn;
        $poll_id = $user_id = $cmt = "";

        if(!empty($_SESSION['poll_id'])) {
            $poll_id = $_SESSION['poll_id'];
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
            $alertMsg = "asst.php: error - could not execute $CHECKEXISTINGDATACMD";
            alertMsg();
            updateAndSave();
            redirectToEditPage();
        }
    }

    function cancelVote() {
        updateAndSave();
        redirectToEditPage();
    }

    function errorGettingPollData() {
        global $alertMsg;
        alertMsg("$alertMsg");
        updateAndSave();
        redirectToEditPage();
        return;
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../user/edit.php';</script>";
        echo $jsRedirect;
        return;
    }

    function alertMsg($msg) {
        $jsAlert = "<script type='text/javascript' ";
        $jsAlert .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsAlert .= "<script>alert('$msg');</script>";
        echo $jsAlert;
        return;
    }

    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
<head>
<title>Faculty Confidential Advisory Vote To The Chair</title>
<style>
	.preface {
		color: #3333ff;
	}
</style>
</head>
<body>
<form style="width:70%; margin: 0 auto" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
<h1 align="center"><u>Confidential Document</u></h1>
<p align="center"><?php echo $dataErrorMsg; ?></p>
<h4 align="center">*NON VOTING FACULTY MEMBERS</h4>
<h4 align="center">CONFIDENTIAL EVALUATION FORM</h4>
<div>
    <p class="preface">
        <strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
        faculty member wishes to remain anonymous and/or will not be able to attend the meeting 
        and would like the comments brought up at the meeting for discussion.<br>
    </p>
    <p class="preface">
        [Use this statement for ECE and CEE confidential evals instead of the above <strong>NOTE..:</strong>
        Comment may be submitted to the chair prior to the department meeting if the faculty member will
        not be able to attend the meeting and would like the comments brought up at the meeting for discussion.]<br>
    </p>

    <p class="preface">
        Comments not discussed at the meeting will not be reflected in the department letter.
    </p>
</div>
<hr/>
<div>
    <p> 
        Merit, Promotions, or Fith-year appraisal for 
        <?php if(!empty($_SESSION['profName']) && !empty($_SESSION['pollType'])) {
                echo $_SESSION['profName']."'s ".$_SESSION['pollType'].'.';
                } ?> 
    </p>
    Confidential comments regarding Merrit, Promotions or Fifth-year appraisals.</br>
    <textarea name="comment" id="comment" rows="8" style="width:100%"><?php 
            // PHP open tag above
            if(!empty($_SESSION['cmt'])) { echo $_SESSION['cmt']; } 
        ?></textarea>
</div>
<hr/>
<p> Comments must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the 
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<div id="actionDiv">
    <input type="submit"  name="action" value="Cancel">
    <input type="submit" name="action" value="Submit">
</div>
</form>
<!-- End form -->
<!-- Scripting begins -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
	$( "#effDate" ).datepicker( { dateFormat: 'yy-mm-dd' } );
});
</script>
</body>