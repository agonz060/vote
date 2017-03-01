<?php
    // review.php : 
    // Displays a user's voting history
    session_start();
    //var_dump($_SESSION);
    //echo 'one';
    if(idleLimitReached()) {
        //echo 'one';
        signOut();
    } else {
        //echo 'two';
        //unsetPollVariables();
        //timeSinceLastActivity();
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

    function updateAndSaveSession() {
        updateLastActivity();
        saveSessionVars();
    }

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>";
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
    require_once '../event/connDB.php';
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $HOME = "home";
        $EDIT = "edit";
        $SIGN_OUT = "signOut";
        $menuOption = "";

        if(!empty($_POST['menu'])) {
            $menuOption =$_POST['menu'];
            if($menuOption == $HOME) {
                updateAndSaveSession();
                redirectToHomePage();
            } else if($menuOption == $EDIT) {
                updateAndSaveSession();
                redirectToEditPage();
            } else if($menuOption == $SIGN_OUT) {
                signOut();
            }
        }
    } // End of processing $_POST data

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
            return -1;
        }

        // Get all polls the user has voted in or polls the user was included in
        // but did not participate in
        $SELECTCMD = "SELECT poll_id FROM Voters WHERE (user_id=$user_id ";
        $SELECTCMD .= "AND voteFlag=1)";
        $result = mysqli_query($conn,$SELECTCMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $ids[] = $row['poll_id'];
            }
            //echo "ids: "; print_r($ids);
            return $ids;
        } else { // Error executing select command
            return -1;
        }
    }
    function getMeritData($poll_id) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Merit_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Merit Data table';
            alertMsg($msg);
            return -1;
	}
    }
    function getReappointmentData($poll_id) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Reappointment_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; pr  int_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Reappointment Data table';
            alertMsg($msg);
            return -1;
        }
    }
    function getFifthYearReviewData($poll_id) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Fifth_Year_Review_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; pr  int_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Fifth Year Review Data table';
            alertMsg($msg);
            return -1;
        }
    }
    function getFifthYearAppraisalData($poll_id) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Fifth_Year_Appraisal_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Fifth Year Appraisal Data table';
            alertMsg($msg);
            return -1;
        }
    }
    function getPromotionData($pollId) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Associate_Promotion_Data WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$pollId";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Promotion Data table';
            alertMsg($msg);
            return -1;
        }
    }

    function getAssistantData($pollId) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
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
                $data = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Assistant Data table';
            alertMsg($msg);
            return -1;
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

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='edit.php'</script>";
        echo $jsRedirect;
        return;
    }
?>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
    .button-home {
        text-align: center;
        color: white;
        background: rgb(224,224,224);
        width: 160px;
    }
    .button-edit {
        text-align: center;
        color: white;
        background: rgb(66,140,244);
        width: 160px;
    }
    .button-view {
        text-align: center;
        color: white;
        background: rgb(28,184,65);
        width: 160px;
    }
    .button-signOut {
        text-align: center;
        color: white;
        background: rgb(202,60,60);
        width: 160px;
    }
</style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li><a href="edit.php">Edit Poll</a></l>
			<li class="active"><a href="review.php">Review Poll</a></li>
		</ul>
	</div>
</nav>
<div class="container">
    <table class="table table-responsive table-hover table-bordered" align="center">
        <thead>
            <tr>
                <th>Regarding</th>
                <th>Type of Poll</th>
                <th>Poll End Date</th>
                <th>View</th>
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
                $poll_id = $title = $endDate  = "";
                $name = $effDate = $pollType = $dept = "";
                $pollData = array();
                $READ_ONLY = 1;
                // Search for user_id in Voters, then select all poll_id from
                // Voters where voteFlag = 1
                $ids = getPollIDs();
                if($ids == -1) {
                    $msg = "edit.php: error executing SELECTCMD in getPollIDs().";
                    $msg .= " Redirecting to user home page...";
                    alertMsg($msg);
                    updateAndSaveSession();
                    redirectToHomePage();
                } else {
                    foreach($ids as $id) {
                        // Only display inactive polls
                        $selectCmd = "SELECT * FROM Polls WHERE poll_id=$id";
                        //$selectCmd .= "OR CURDATE() >= deactDate";

                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying
                            while($row = $result->fetch_assoc()) {
                                $poll_id = $row["poll_id"];
                                $deactDate = $row["deactDate"];
                                $name = $row["name"];
                                $pollType = $row["pollType"];
                                $dept = $row["dept"];
                                $effDate = $row["effDate"];
                                $pollData = array("READ_ONLY" => $READ_ONLY,
                                                "poll_id" => $poll_id,
                                                "deactDate" => $deactDate,
                                                "name" => $name,
                                                "pollType" => $pollType,
                                                "dept" => $dept,
                                                "effDate" => $effDate );
                                //echo "pollData: ";print_r($pollData);
                                echo "<tr>
                                        <td>".
                                            $pollData['name']
                                            ."
                                        </td>
                                        <td>".
                                            $pollData['pollType'].
                                        "</td>
                                        <td>".
                                            $pollData['deactDate'].
                                        "</td>
                                        <td>";
                                        if(!empty($_SESSION['title'])) {
                                            // Poll types
                                            $MERIT = "Merit";
                                            $PROMOTION = "Promotion";
                                            $REAPPOINTMENT = "Reappointment";
                                            $FIFTH_YEAR_REVIEW = "Fifth Year Review";
                                            $FIFTH_YEAR_APPRAISAL = "Fifth Year Appraisal";

                                            // Vars
                                            $redirect = $voteData = '';
                                            $title = $_SESSION['title'];
                                            $pollType = $pollData['pollType'];
                                            $poll_id = $pollData['poll_id'];
                                            $pollData = json_encode($pollData);
                                            //echo "json_encode(pollData): $pollData";
                                            if($title == $ASST) {
                                                if($pollType == $MERIT) {
                                                    $voteData = getMeritData($poll_id);
                                                    $redirect = '../forms/merit.php';
                                                } else { // Only other form available to Assistant professors 
                                                    $voteData = getAssistantData($poll_id);
                                                    $redirect = '../forms/asst.php';
                                                }
                                            } else if($title == $ASSOC || $title == $FULL) {
                                                if($pollType == $MERIT) {
                                                    $voteData = getMeritData($poll_id);
                                                    $redirect = '../forms/merit.php';
                                                } else if($pollType == $PROMOTION) {
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
                                        <form method='post' id='editPoll_$poll_id' action='$redirect'>
                                            <button class='btn btn-success'>View</button>
                                            <input type='hidden' name='pollData' value='$pollData'>
                                            <input type='hidden' name='_voteData' value='$voteData'>
                                        </form>
                                    </td>           
                                </tr>";
                            } // End of while loop
                        } else { // Error executing select cmd 
                            $msg = "edit.php: error when executing selectCmd.\n";
                            $msg .= "Could not display table. Redirecting to home page...";
                            alertMsg($msg);
                            updateAndSaveSession();
                            redirectToHomePage();
                        }
                    } // End of displaying user edit table
                }
            ?>
        </tbody>
    </table>
</div>
</body>
<!-- End of HTML body -->
