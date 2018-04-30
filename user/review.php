<?php
    session_start();

    require_once '../includes/connDB.php';
    require_once '../includes/functions.php';

	//var_dump($_SESSION);
    //echo 'one';
    if(idleLimitReached()) {
        //echo 'one';
        signOut();
    } else {
        //echo 'two';
        updateLastActivity();
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

    // function getActionCount($pollId) {
    //     global $conn;
    //     $actionCount = 0;
    //     $query = "SELECT count(action_num) FROM Poll_Actions WHERE poll_id=?";
    //     $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
    //     mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_result($stmt, $actionCount) or die(mysqli_error($conn));
    //     mysqli_stmt_fetch($stmt);
    //     mysqli_stmt_close($stmt);
    //     return $actionCount;
    // }

    // function getActionInfo($pollId) {
    //     global $conn;
    //     $actionInfoArray = array();
    //     $fromTitle = $fromStep = $toTitle = $toStep = $accelerated = "";
    //     $query = "SELECT fromTitle,fromStep,toTitle,toStep,accelerated FROM Poll_Actions WHERE poll_id=?";
    //     $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
    //     mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_result($stmt,$fromTitle,$fromStep,$toTitle,$toStep,$accelerated) or die(mysqli_error($conn));
    //     while(mysqli_stmt_fetch($stmt)) {
    //         $actionInfo = array( "fromTitle" => $fromTitle,
    //                         "fromStep" => $fromStep,
    //                         "toTitle" => $toTitle,
    //                         "toStep" => $toStep,
    //                         "accelerated" => $accelerated );
    //         $actionInfoArray[] = $actionInfo;
    //     }
    //     mysqli_stmt_close($stmt);
    //     return $actionInfoArray;
    // }

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
            } elseif($menuOption == $EDIT) {
                updateAndSaveSession();
                redirectToEditPage();
            } elseif($menuOption == $SIGN_OUT) {
                signOut();
            }
        }
    } // End of processing $_POST data

    // Helper functions
    // Gets the poll id's the user has voted on or the poll has expired
    // function getPollIDs() {
    //     global $conn;
    //     $ids = array();
    //     $user_id = "";

    //     if(!empty($_SESSION['user_id'])) {
    //         $user_id = $_SESSION['user_id'];
    //     } else {
    //         $msg = 'review.php: error - user_id not set. Redirecting to log in...';
    //         alertMsg($msg);
    //         signOut();
    //         return -1;
    //     }

    //     // Get all polls the user has voted in or polls the user was included in
    //     // but did not participate in
    //     $SELECTCMD = "SELECT poll_id FROM Voters WHERE (user_id=$user_id ";
    //     $SELECTCMD .= "AND voteFlag=1)";
    //     $result = mysqli_query($conn,$SELECTCMD);
    //     if($result) {
    //         while($row = $result->fetch_assoc()) {
    //             $ids[] = $row['poll_id'];
    //         }
    //         //echo "ids: "; print_r($ids);
    //         return $ids;
    //     } else { // Error executing select command
    //         return -1;
    //     }
    // }
    function getMeritData($poll_id) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Merits WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
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

        $SELECT_CMD = "SELECT * FROM Reappointments WHERE user_id=$id AND ";
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

        $SELECT_CMD = "SELECT * FROM Fifth_Year_Reviews WHERE user_id=$id AND ";
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

        $SELECT_CMD = "SELECT * FROM Fifth_Year_Appraisals WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$poll_id";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            echo 'Vote data: '; print_r($data);

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

        $SELECT_CMD = "SELECT * FROM Promotions WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$pollId";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
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

    function getEvaluations($pollId) {
        if(empty($_SESSION['user_id'])) {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
        }
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM Confidential_evals WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$pollId";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            // echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Assistant Data table';
            alertMsg($msg);
            return -1;
        }
    }
    function getOtherData($pollID) {
        global $conn;
        $data = array();
        $id = $_SESSION['user_id'];

        $SELECT_CMD = "SELECT * FROM other_polls WHERE user_id=$id AND ";
        $SELECT_CMD .= "poll_id=$pollId";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;
        } else { // Error occured while executing command
            $msg = 'review.php: error - failure while retreiving data from ';
            $msg .= 'Other Data table';
            alertMsg($msg);
            return -1;
        }
    }
    function getUserForm(&$pollData) {
        $uTitle = $_SESSION['title'];
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
    // function getDataTable($pollType) {
    //     // Replace spaces for indexing purposes
    //     $pollType = str_replace(' ','_',$pollType);
    //     // Create array of database tables with the pollType as an index
    //     $tables = array('Fifth_Year_Review' => 'fifth_year_review_data',
    //                     'Fifth_Year_Appraisal' => 'fifth_year_appraisal_data',
    //                     'Merit' => 'merit_data',
    //                     'Promotion' => 'associate_promotion_data',
    //                     'Reappointment' => 'reappointment_data',
    //                     'Other' => 'other_poll_data');
    //     // Return table
    //     return $tables[$pollType];
    // }
    function formatComment($comment) {
        $QUOTES_HTML = '&quot;';
        // Replace single and double quotes with hmtl equivalent to avoid errors when json_decoding comments
        $comment = str_replace("\"",$QUOTES_HTML,$comment);
        $comment = htmlspecialchars($comment,ENT_QUOTES);
        return $comment;
    }
    function getUserVoteData($poll_id,$pollType,$getComments=false) {
        // set up variables
        global $conn;
        $FALSE = false;

        $comment = $actionNum = "";
        $data = $row = $multiActionData = array();
        $id = $_SESSION['user_id'];
        // Get data table to extract data from from
        // If getComments flag set then a different table is selected regardless of poll type
        if($getComments) {
            $table = "confidential_evals";
        } else {
            $table = getDataTable($pollType);
        }
        // These poll types have multiple actions
        if($getComments == $FALSE && ($pollType == 'Merit' || $pollType == 'Promotion' || $pollType == 'Other')) {
            // Group results by action num
            $query = "SELECT * FROM $table WHERE user_id=$id AND poll_id=$poll_id GROUP BY action_num ASC";
            // print $query;
            // Fetch results
            if($result = mysqli_query($conn, $query)) {
                while($row = $result->fetch_assoc()) {
                    // Convert special characters into html equivalent to avoid errors when json encoding/decoding data
                    $comment = formatComment($row['voteCmt']);
                    $data = array('voteCmt' => $comment,
                                    'vote' => $row['vote']);
                    $multiActionData[] = $data;
                } // End of while loop
                // Clear $data array (just in case), then transfer $multiactiondata to $data
                $data = null;
                $data = $multiActionData;
            } else { // Error occurred while executing query
                print "Error: " . mysqli_error($conn);
            }
        } else { // The rest of these poll types are single action polls
            // set up query
            $query = "SELECT * FROM $table WHERE user_id=$id AND poll_id=$poll_id";
            // Convert special characters into html equivalent to avoid errors when json encoding/decoding data
            if($result = mysqli_query($conn, $query)) {
                $row = $result->fetch_assoc();
                //print "In single action poll <br>"; print_r($row); print "<hr>";
                $data = $row;
                if($getComments == $FALSE && $pollType == 'Fifth Year Review') {
                    if(isset($row['qualificationsCmt'])) {
                        // $comment = str_replace("\"",$QUOTES_HTML,$row['']);
                        $comment = $row['qualificationsCmt'];
                        if(strlen($comment) > 0) {
                            $data['qualificationsCmt'] = formatComment($comment);
                            // $data['qualificationsCmt'] = htmlspecialchars($comment,ENT_QUOTES);
                        }
                    }
                    $comment = $row['voteCmt'];
                    if(strlen($comment) > 0) {
                        $data['voteCmt'] = formatComment($comment);
                        // $data['voteCmt'] = htmlspecialchars($comment,ENT_QUOTES);
                    }
                } else if($getComments == $FALSE && $pollType == 'Fifth Year Appraisal') {
                    if(isset($row['teachingCmts'])) {
                        $comment = $row['teachingCmts'];
                        if(strlen($comment) > 0) {
                            $data['teachingCmts'] = formatComment($comment);
                            // $data['teachingCmts'] = htmlspecialchars("$comment",ENT_QUOTES);
                        }
                    }
                    if(isset($row['researchCmts'])) {
                        $comment = $row['researchCmts'];
                        if(strlen($comment) > 0) {
                            $data['researchCmts'] = formatComment($comment);
                            // $data['researchCmts'] = htmlspecialchars("$comment",ENT_QUOTES);
                        }
                    }
                    if(isset($row['pubServiceCmts'])) {
                        $comment = $row['pubServiceCmts'];
                        if(strlen($comment) > 0) {
                            $data['pubServiceCmts'] = formatComment($comment);
                            // $data['pubServiceCmts'] = htmlspecialchars("$comment",ENT_QUOTES);
                        }
                    }
                } else { // either getting comments from confidential_evals table or pollType = reappointment
                    $comment = $row['voteCmt'];
                    // print $comment . "<br>";
                    if(strlen($comment) > 0) {
                         $data['voteCmt'] = formatComment($comment);
                        // $data['voteCmt'] = htmlspecialchars($comment,ENT_QUOTES);
                    }
                    // print "special: " . htmlspecialchars($comment,ENT_QUOTES) . "<br>";
                }
            } else { // Error occured while executing query
                print "Error: " . mysqli_error($conn);
            }
        } // End of else
        // Encoding data eases the data transfering process
        return json_encode($data);
    }
    function getMultiActionPollData($poll_id,$pollType) {
        // set up variables
        global $conn;
        $comment = "";
        $data = $row = array();
        $id = $_SESSION['user_id'];
        $table = getDataTable($pollType);
        if($getComments) {
            $table = "assistant_data";
        }
        // set up query
        $query = "SELECT vote,voteCmt,action_num FROM $table WHERE user_id=$id AND poll_id=$poll_id";

    }

    function getSubmissionDate($poll_id) {
        global $conn;
        $user_id = $_SESSION['user_id'];
        $errorMsg = "An error occured while retreiving submission date. Please contact systems@engr.ucr.edu";
        $getSubmissionQuery = "SELECT DATE_FORMAT(submissionDate, '%M %D %Y') as subDate from voters where poll_id={$poll_id} and user_id={$user_id}";
        $submissionDate = '';

        if($result = mysqli_query($conn,$getSubmissionQuery)) {
            $row = $result->fetch_assoc();
            $submissionDate = $row['subDate'];
        } else {
            $submissionDate = $errorMsg;
        }
        return $submissionDate;
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
<title>Review Polls</title>
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
                <th>Submission Date</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Constants
                $GET_COMMENTS = true;
                $READ_ONLY = 1;
                // Forms
                $REGULAR_FORM = 1;
                $ADVISORY_FORM = 2;
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

                if(empty($_SESSION['title'])) {
                    $msg = 'User title not set. Redirecting to log in page...';
                    alertMsg($msg);
                    signOut();
                }
                // Poll data
                $poll_id = $title = $endDate  = "";
                $name = $effDate = $pollType = $dept = $profTitle = "";
                $pollData = array();

                // Search for user_id in Voters, then select all poll_id from
                // Voters where voteFlag = 1
                $ids = getOldPollIDs();
                $pollTypes = getPollTypes();
                $depts = getDepartments();

                if($ids == -1) {
                    $msg = "edit.php: error executing SELECTCMD in getPollIDs().";
                    $msg .= " Redirecting to user home page...";
                    alertMsg($msg);
                    updateAndSaveSession();
                    redirectToHomePage();
                } else {
                    foreach($ids as $id) {
                        // Only display inactive polls
                        $submissionDate = getSubmissionDate($id);
                        $selectCmd = "SELECT * FROM Polls WHERE poll_id=$id";
                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying
                            while($row = $result->fetch_assoc()) {
                                // Set table variables (for display)
                                $pollData = $row;
                                $poll_id = $row["poll_id"];
                                $deactDate = $row["deactDate"];
                                $name = $row["name"];
                                $pollType = $pollTypes[$row["pollType"]];
                                $pollData['dept'] = $depts[$row["dept"]];
                                // var_dump($pollData['dept']);
                                $pollData['pollType'] = $pollType;
                                $effDate = $row["effDate"];
                                $profTitle = $row['profTitle'];
                                $pollData['READ_ONLY'] = $READ_ONLY;
                                $pollData['userTitle'] = $_SESSION['title'];
                                // Get additional pollData if neccessary
                                if($pollType == $MERIT || $pollType == $PROMOTION || $pollType == $OTHER) {
                                    $numActions = getActionCount($id);
                                    $actionInfoArray = getActionInfo($id);
                                    $pollData['numActions'] = $numActions;
                                    $pollData['actionInfoArray'] = $actionInfoArray;
                                    //print_r($actionInfoArray);
                                }
                                // Encode $pollData array for storage/transfer
                                $pollDataEncoded = json_encode($pollData);
                                // End Loading poll data
                                echo "<tr>
                                        <td>".
                                            $pollData['name']
                                            ."
                                        </td>
                                        <td>".
                                            $pollType.
                                        "</td>
                                        <td>".
                                            $submissionDate.
                                        "</td>
                                        <td>";
                                        if(isset($_SESSION['title'])) {
                                            // Relative path to forms
                                            //$ASST_FORM = '../forms/asst.php';
                                            $EVALUATION_FORM_LINK = "../forms/evaluation.php";
                                            $ADVISORY_VOTE_FORM_LINK = "../forms/assoc_full.php";
                                            $REAPPOINTMENT_FORM_LINK = "../forms/reappointment.php";
                                            $PROMOTION_FORM_LINK = '../forms/promotion.php';
                                            $MERIT_FORM_LINK = '../forms/merit.php';
                                            $BALLOT_FORM_LINK = "../forms/ballot.php";
                                            $FIFTH_YEAR_REVIEW_FORM_LINK = "../forms/quinquennial.php";
                                            $FIFTH_YEAR_APPRAISAL_FORM_LINK = "../forms/fifthYearAppraisal.php";
                                            // Variables
                                            $redirect = $voteData = '';
                                            $poll_id = $pollData['poll_id'];
                                            // Get form information to determine where to redirect the user
                                            $userForm = getUserForm($pollData);
                                            //print "userform: $userForm <br>";
                                            // Logic structure to determine who votes on what form
                                            if($userForm == $REGULAR_FORM || $userForm == $ADVISORY_FORM) {
                                                switch($pollType) {
                                                    case $REAPPOINTMENT:
                                                        // $voteData = getReappointmentData($poll_id);
                                                        $redirect = $REAPPOINTMENT_FORM_LINK;
                                                        break;
                                                    case $PROMOTION:
                                                        // $voteData = getPromotionData($poll_id);
                                                        $redirect = $PROMOTION_FORM_LINK;
                                                        break;
                                                    case $FIFTH_YEAR_REVIEW:
                                                        // $voteData = getFifthYearReviewData($poll_id);
                                                        $redirect = $FIFTH_YEAR_REVIEW_FORM_LINK;
                                                        break;
                                                    case $MERIT:
                                                        // $voteData = getMeritData($poll_id);
                                                        $redirect = $MERIT_FORM_LINK;
                                                        break;
                                                    case $FIFTH_YEAR_APPRAISAL:
                                                        // $voteData = getFifthYearAppraisalData($poll_id);
                                                        $redirect = $FIFTH_YEAR_APPRAISAL_FORM_LINK;
                                                        break;
                                                    case $OTHER:
                                                        // $voteData = getOtherData($poll_id);
                                                        $redirect = $BALLOT_FORM_LINK;
                                                        break;
                                                    default:
                                                        $MSG = '"Could not redirect to proper form. Please contact systems at systems@engr.ucr.edu."';
                                                        $ALERT_FCT = "<script>alert($MSG);</script>";
                                                        echo $ALERT_FCT;
                                                } // End switch
                                                if(isset($redirect)) {
                                                    $voteData = getUserVoteData($poll_id,$pollType);
                                                }
                                            } else { // $userForm == $EVALUATION_FORM
                                                    $voteData = getUserVoteData($poll_id,$pollType,$GET_COMMENTS);
                                                    $redirect = $EVALUATION_FORM_LINK;
                                            }
                                        } // End of isset($_SESSION['title'])
                                        // echo htmlspecialchars("This is some <b>bold</b> text.",ENT_QUOTES);

                                        //print_r($voteData);
                                        echo"
                                        <form method='post' id='editPoll_$poll_id' action='$redirect'>
                                            <button class='btn btn-success'>View</button>
                                            <input type='hidden' name='pollData' value='$pollDataEncoded'>
                                            <input type='hidden' name='voteData' value='$voteData'>
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
                        } // End of else() (error executing select)
                    } // End of foreach(...) (End of displaying user edit table)
                } // End of else
            ?>
        </tbody>
    </table>
</div>
</body>
<!-- End of HTML body -->
