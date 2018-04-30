<?php
    session_start();

	require_once "../includes/connDB.php";
    require_once "../includes/functions.php";

    //var_dump($_SESSION);

    if(idleTimeLimitReached()) {
        signOut();
    } else {
        //timeSinceLastActivity();
        updateLastActivity();
    }

    function timeSinceLastActivity() {
        $t = time() - $_SESSION['LAST_ACTIVITY'];
        //echo "Time since last activity: $t";
        return;
    }

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

    function updateAndSaveSession() {
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

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $HOME = "home";
        $REVIEW = "review";
        $SIGN_OUT = "signOut";
        $menuOption = "";

        if(!empty($_POST['menu'])) {
            $menuOption =$_POST['menu'];
            if($menuOption == $HOME) {
                updateAndSaveSession();
                redirectToHomePage();
            } elseif($menuOption == $REVIEW) {
                updateAndSaveSession();
                redirectToReviewPage();
            } elseif($menuOption == $SIGN_OUT) {
                signOut();
            }
        }
    } // End of processing $_POST data

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

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='review.php'</script>";
        echo $jsRedirect;
        return;
    }

    function getUserForm(&$pollData) {
        $ASSISTANT = "Assistant Professor";
        $ASSOCIATE = "Associate Professor";
        $FULL = "Full Professor";

        $uTitle = $_SESSION['title'];
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
?>
<head>
<title>Edit Polls</title>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

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
        background: rgb(28,184,65);
        width: 160px;
    }
    .button-review {
        text-align: center;
        color: white;
        background: rgb(66,140,244);
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
			<li class="active"><a href="edit.php">Edit Poll</a></li>
			<li><a href="review.php">Review Poll</a></li>
		</ul>
	</div>
</nav>
   <div class="container">
    <table class="table table-responsive table-hover table-bordered" align="center">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Regarding</th>
                <th>Type of Poll</th>
                <th>Poll  End Date</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php
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

                if(empty($_SESSION['title'])) {
                    $msg = 'User title not set. Redirecting to log in page...';
                    alertMsg($msg);
                    signOut();
                }
                // Poll data
                $poll_id = $title = $description = $endDate  = "";
                $name = $effDate = $pollType = $dept = $profTitle = "";
                $pollTypes = getPollTypes();
                $depts = getDepartments();
                // var_dump($pollTypes);
                $ids = getNewPollIDs();
                if($ids == -1) {
                    $msg = "edit.php: error executing SELECTCMD in getPollIDs().";
                    $msg .= " Redirecting to user home page...";
                    alertMsg($msg);
                    updateAndSaveSession();
                    redirectToHomePage();
                } else {
                    foreach($ids as $id) {
                	   $pollData = array();
			             // Only display inactive polls
                        $selectCmd = "SELECT * FROM Polls WHERE CURDATE() <= deactDate AND poll_id=$id";
                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying
                            //print("1");
                            while($row = $result->fetch_assoc()) {
                                //print("2");
                                // Start loading poll data
                                $numActions = 0;
                                $actionInfoArray = 0;
                                $pollType = $pollTypes[$row['pollType']];
                                $profTitle = $row['profTitle'];
                                $pollData = $row;
                                $pollData['pollType'] = $pollType;
                                $pollData['dept'] = $depts[$pollData['dept']];
                                //print_r($pollData);
                                if($pollType == $MERIT || $pollType == $PROMOTION || $pollType == $OTHER) {
                                    $numActions = getActionCount($id);
                                    $actionInfoArray = getActionInfo($id);
                                    var_dump($actionInfoArray);
                                    $pollData['numActions'] = $numActions;
                                    $pollData['actionInfoArray'] = $actionInfoArray;
                                    $pollData['userTitle'] = $_SESSION['title'];
                                    //print_r($actionInfoArray);
                                }
                                // Encode $pollData array for storage/transfer
                                $pollDataEncoded = json_encode($pollData);
                                $voteData = json_encode(null);
                                // End Loading poll data
                                // Start displaying table
                                $pollTitle = $row["title"];
                                $description = $row["description"];
                                echo "<tr>
                                        <td>
                                            $pollTitle
                                        </td>
                                        <td>
                                            $description
                                        </td>
                                        <td>".
                                            $pollData['name']
                                        ."</td>
                                        <td>".
                                            $pollType
                                        ."</td>
                                        <td>".
                                            $pollData['deactDate']
                                        ."</td>
                                        <td>";
                                        if(isset($_SESSION['title'])) {
                                            // Relative path to forms
                                            //$ASST_FORM_LINK = '../forms/asst.php';
                                            $EVALUATION_FORM_LINK = "../forms/evaluation.php";
                                            //$ADVISORY_VOTE_FORM_LINK = "../forms/assoc_full.php";
                                            $REAPPOINTMENT_FORM_LINK = "../forms/reappointment.php";
                                            $PROMOTION_FORM_LINK = '../forms/promotion.php';
                                            $MERIT_FORM_LINK = '../forms/merit.php';
                                            $FIFTH_YEAR_REVIEW_FORM_LINK = "../forms/quinquennial.php";
                                            $FIFTH_YEAR_APPRAISAL_FORM_LINK = '../forms/fifthYearAppraisal.php';
                                            $BALLOT_FORM_LINK = "../forms/ballot.php";
                                            // Variables
                                            $redirect = '';
                                            $userForm = getUserForm($pollData);
                                            //print("userForm: $userForm");
                                            //print("pollType: $pollType userTitle: $userTitle");
                                            // Logic structure to determine who
                                            if($userForm == $REGULAR_FORM || $userForm == $ADVISORY_FORM) {
                                                switch($pollType) {
                                                    case $REAPPOINTMENT:
                                                        $redirect = $REAPPOINTMENT_FORM_LINK;
                                                        break;
                                                    case $PROMOTION:
                                                        $redirect = $PROMOTION_FORM_LINK;
                                                        break;
                                                    case $FIFTH_YEAR_REVIEW:
                                                        $redirect = $FIFTH_YEAR_REVIEW_FORM_LINK;
                                                        break;
                                                    case $MERIT:
                                                        $redirect = $MERIT_FORM_LINK;
                                                        break;
                                                    case $FIFTH_YEAR_APPRAISAL:
                                                        $redirect = $FIFTH_YEAR_APPRAISAL_FORM_LINK;
                                                        break;
                                                    case $OTHER:
                                                        $redirect = $BALLOT_FORM_LINK;
                                                        break;
                                                    default:
                                                        $MSG = '"Could not redirect to proper form. Please contact systems at systems@engr.ucr.edu."';
                                                        $ALERT_FCT = "<script>alert($MSG);</script>";
                                                        echo $ALERT_FCT;
                                                } // End switch
                                            } else { // $userForm == $EVALUATION_FORM
                                                    $redirect = $EVALUATION_FORM_LINK;
                                            }
                                            // End of form redirction logic structure  */
                                        } else { // $_SESSION['title'] not set, have user
                                                // log in to reload data
                                            $msg = "edit.php: error - user title not set.\n";
                                            $msg .= "Redirecting to log in page...";
                                            alertMsg($msg);
                                            signOut();
                                        }
                                        // Set voteData to null since no previous user data should exist for new polls
                                        echo"
                                        <form method='post' id='editForm' action='$redirect'>
                                            <button class='btn btn-success'>Edit</button>
                                            <input type='hidden' name='pollData' value='{$pollDataEncoded}'>
                                            <input type='hidden' name='voteData' value='{$voteData}'>
                                        </form>
                                    </td>
                                </tr>";
                            } // End of while loop (each loop displays one row in the table)
                            // End displaying table
                        } else { // Error executing select cmd
                            $msg = "edit.php: error when executing selectCmd.\n";
                            $msg .= "Could not display table. Redirecting to home page...";
                            alertMsg($msg);
                            updateAndSaveSession();
                            redirectToHomePage();
                        }
                    } // End of displaying user edit table
                } // End of else (when id != -1)
            // End of PHP ?>
        </tbody>
    </table>
</div>
</body>
<script>
    function userAlert(msg) {
        alert(msg);
    }
</script>
<!-- End of HTML body -->
