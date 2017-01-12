<?php
    // review.php : 
    // Displays a user's voting history
    session_start();
    var_dump($_SESSION);
    //echo 'one';
    if(idleLimitReached()) {
        //echo 'one';
        signOut();
    } else {
        //echo 'two';
        $READ_ONLY = true;
        unsetPollVariables();
        $_SESSION["READ_ONLY"] = $READ_ONLY;
        timeSinceLastActivity();
        updateLastActivity();
    }

    function timeSinceLastActivity() {
        $t = time() - $_SESSION['LAST_ACTIVITY'];
        echo "Time since last activity: $t";
        return;
    }

    function idleLimitReached() {
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

    function updateAndSave() {
        updateLastActivity();
        saveSessionVars();
    }

    function unsetPollVariables() {
        // Session variables accessed
        $CMT = "cmt";
        $PROF_NAME = "profName";
        $DESCRIPTION = "description";
        $EFF_DATE = "effDate";
        $POLL_ID = "poll_id";
        $POLL_TYPE = "pollType";
        $PROF_NAME = "profName";
        $ACT_DATE = "actDate";
        $DEACT_DATE = "deactDate";
        $READ_ONLY = "READ_ONLY";
        $DEPT = "dept";

        unset($GLOBALS['_SESSION'][$PROF_NAME]);
        unset($GLOBALS['_SESSION'][$DESCRIPTION]);
        unset($GLOBALS['_SESSION'][$CMT]);
        unset($GLOBALS['_SESSION'][$POLL_ID]);
        unset($GLOBALS['_SESSION'][$POLL_TYPE]);
        unset($GLOBALS['_SESSION'][$PROF_NAME]);
        unset($GLOBALS['_SESSION'][$DEACT_DATE]);
        unset($GLOBALS['_SESSION'][$READ_ONLY]);
        unset($GLOBALS['_SESSION'][$DEPT]);

    }

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='index.php'</script>";
        echo $jsRedirect;
        return;
    }
    
    function signOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();
        saveSessionVars();

        // Save, redirect
        redirectToLogIn();
    }
/* End of session verification */
?>
<?php 
    require_once '../event/connDB.php';
    // Helper functions
    // Gets the poll id's the user has voted on or the poll has expired
    function getPollIDs() {
        global $conn;
        $ids = array();
        $user_id = "";

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else { 
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }

        // Get all polls the user has voted in or polls the user was included in
        // but did not participate in
        $SELECTCMD = "SELECT poll_id FROM Voters WHERE user_id=$user_id ";
        $SELECTCMD .= "AND (voteFlag=1 OR (CURDATE() > pollEndDate))";
        $result = mysqli_query($conn,$SELECTCMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $ids[] = $row['poll_id'];
            }
            //return $ids;
        } else { // Error executing select command
            return -1;
        }
    }

    function getAssistantData($pollId) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set line 130. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Assistant_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$pollId";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo 'Vote data: '; print_r($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while executing SELECT_CMD line 143.';
            alertMsg($msg);

        }
    }

    function alertMsg($msg) {
        $jsAlert = "<script type='text/javascript' ";
        $jsAlert .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsAlert .= "<script>alert('$msg');</script>";
        echo $jsAlert;
        return;
    }

    function redirectToHomePage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='home.php'</script>";
        echo $jsRedirect;
        return;
    }
?>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
    .button-edit {
        color: white;
        background: rgb(28,184,65); 
        width: 80px;
    }
</style>
</head>
<body>
    <table class="pure-table pure-table-bordered" align="center">
        <thead>
            <tr>
                <th>Regarding</th>
                <th>Type of Poll</th>
                <th>Poll End Date</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Titles
                $ASST = "Assistant Professor";
                $ASSOC = "Associate Professor";
                $FULL = "Full Professor";

                if(empty($_SESSION['title'])) {
                    $msg = 'User title not set. Redirecting to log in page...';
                    alertMsg($msg);
                    signOut();
                }

                // Poll data
                $poll_id = $title = $description = $endDate  = "";
                $name = $effDate = $pollType = $dept = "";

                // Search for user_id in Voters, then select all poll_id from
                // Voters where voteFlag = 1
                $ids = getPollIDs();
                if($ids == -1) {
                    $msg = "edit.php: error executing SELECTCMD in getPollIDs().";
                    $msg .= " Redirecting to user home page...";
                    alertMsg($msg);
                    updateAndSave();
                    redirectToHomePage();
                } else {
                    foreach($ids as $id) {
                        // Only display inactive polls
                        $selectCmd = "SELECT * FROM Polls WHERE poll_id=$id ";
                        $selectCmd .= "OR CURDATE() >= deactDate";

                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying
                            while($row = $result->fetch_assoc()) {
                                $poll_id = $row["poll_id"];
                                $endDate = $row["deactDate"];
                                $name=$row["name"];
                                $pollType=$row["pollType"];
                                $dept=$row["dept"];
                                $effDate=$row["effDate"];
                                echo "<tr>
                                        <td>
                                            $name
                                        </td>
                                        <td>
                                            $pollType
                                        </td>
                                        <td>
                                            $endDate
                                        </td>
                                        <td>";
                                        if(!empty($_SESSION['title'])) {
                                            // Poll types
                                            $MERRIT = "Merrit";
                                            $PROMOTION = "Promotion";
                                            $REAPPOINTMENT = "Reappointment";
                                            $FIFTH_YEAR_REVIEW = "Fifth Year Review";
                                            $FIFTH_YEAR_APPRAISAL = "Fifth Year Appraisal";

                                            // Vars
                                            $redirect = $voteData = '';
                                            $title = $_SESSION['title'];
                                            $voteData = '';

                                            if($title == $ASST) {
                                                if($pollType == $MERRIT) {
                                                    $redirect = '../forms/merrit.php';
                                                } else { // Only other form available to Assistant professors 
                                                    $voteData = getAssistantData($poll_id);
                                                    $redirect = '../forms/asst.php';
                                                }
                                            } else if($title == $ASSOC || $title == $FULL) {
                                                if($pollType == $PROMOTION) {
                                                    $voteData = getPromotionData($poll_id);
                                                    $redirect = '../forms/assoc_full.php';
                                                } else if($pollType == $REAPPOINTMENT) {
                                                    $voteData = getReappointmentData($poll_id);
                                                    $redirect = '../forms/reappointment.php';
                                                } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                                                    $voteData = getFifthYearAppraisalData($poll_id);
                                                    $redirect = '../forms/fifthYearAppraisal.php';
                                                } else if($polltype == $FIFTH_YEAR_REVIEW) {
                                                    $voteData = getFifthYearReviewData($poll_id);
                                                    $redirect = '../forms/quinquennial.php';
                                                }
                                            } // End of if( $title == ($ASSOC || $FULL) )
                                        } else { // $_SESSION['title'] not set, have user 
                                                // log in to reload data
                                            $msg = "edit.php: error - user title not set.\n";
                                            $msg .= "Redirecting to log in page...";
                                            alertMsg($msg);
                                            signOut();
                                        }
                                        echo"
                                        <form method='post' id='editPoll_$po' action='$redirect'>
                                            <p id='testingRedirect'><font color='green'><h3>Redirect: $redirect</h3></font></p>
                                            <button class='button-edit pure-button'>Edit</button>
                                            <input type='hidden' name='poll_id' value='$poll_id'>
                                            <input type='hidden' name='profName' value='$name'>
                                            <input type='hidden' name='pollType' value='$pollType'>
                                            <input type='hidden' name='dept' value='$dept'>
                                            <input type='hidden' name='effDate' value='$effDate'>
                                            <input type='hidden' name='deactDate' value='$endDate'>";
            
                                        echo"
                                        </form>
                                    </td>           
                                </tr>";
                            } // End of while loop
                        } else { // Error executing select cmd 
                            $msg = "edit.php: error when executing selectCmd.\n";
                            $msg .= "Could not display table. Redirecting to home page...";
                            alertMsg($msg);
                            updateAndSave();
                            redirectToHomePage();
                        }
                    } // End of displaying user edit table
                }
            // End of PHP ?>
        </tbody>
    </table>
</body>
<!-- End of HTML body -->