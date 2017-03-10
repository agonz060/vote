<?php 
    session_start();
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
        unsetPollVariables();
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

    require_once '../event/connDB.php';
    // Start helper functions
    function getActionCount($pollId) {
        global $conn;
        $actionCount = 0;
        $query = "SELECT count(action_num) FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt, $actionCount) or die(mysqli_error($conn));
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $actionCount;
    }

    function getActionInfo($pollId) {
        global $conn;
        $actionInfoArray = array();
        $fromLevel = $toLevel = $accelerated = "";

        $query = "SELECT fromLevel,toLevel,accelerated FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt,$fromLevel,$toLevel,$accelerated) or die(mysqli_error($conn));
        while(mysqli_stmt_fetch($stmt)) {
            $actionInfo = array( "fromLevel" => $fromLevel,
                            "toLevel" => $toLevel,
                            "accelerated" => $accelerated );
            $actionInfoArray[] = $actionInfo;
        }
        mysqli_stmt_close($stmt); 
        return $actionInfoArray;
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
            } else if($menuOption == $REVIEW) {
                updateAndSaveSession();
                redirectToReviewPage();
            } else if($menuOption == $SIGN_OUT) {
                signOut();
            }
        }
    } // End of processing $_POST data

    // Helper functions
    function getPollIDs() {
        global $conn;
        $ids = array();
        $user_id = "";

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else { 
            $msg = 'edit.php: error - user_id not set. Redirecting to log in..';
            alertMsg($msg);
            signOut();
        }

        $SELECTCMD = "SELECT poll_id FROM Voters WHERE user_id=$user_id ";
        $SELECTCMD .= "AND voteFlag=0";
        $result = mysqli_query($conn,$SELECTCMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $ids[] = $row['poll_id'];
            }
            return $ids;
        } else { // Error executing select command
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

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='review.php'</script>";
        echo $jsRedirect;
        return;
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
			<li class="active"><a href="edit.php">Edit Poll</a></l>
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
                $poll_id = $title = $description = $endDate  = "";
                $name = $effDate = $pollType = $dept = $profTitle = "";

                $ids = getPollIDs();
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
                        $selectCmd = "SELECT * FROM Polls WHERE CURDATE() <= deactDate ";
                        $selectCmd .= "AND poll_id=$id";
                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying

                            while($row = $result->fetch_assoc()) {
                                // Start loading poll data
                                $numActions = 0;
                                $actionInfoArray = 0;
                                $pollType = $row['pollType'];
                                $profTitle = $row['profTitle'];
                                $pollData = array( "poll_id" => $row['poll_id'],
                                                "deactDate" => $row['deactDate'],
                                                "name" => $row['name'],
                                                "pollType" => $pollType,
                                                "dept" => $row['dept'],
						                        "effDate" => $row['effDate'],
					                            "profTitle" => $profTitle	
						                        ); // End $pollData array
                                //print_r($pollData);
                                if($pollType == $MERIT || $pollType == $PROMOTION) {
                                    $numActions = getActionCount($id);
                                    $actionInfoArray = getActionInfo($id);
                                    $pollData['numActions'] = $numActions;
                                    $pollData['actionInfoArray'] = $actionInfoArray;
                                    //print_r($actionInfoArray);
                                }
                                // Encode $pollData array for storage/transfer
                                $pollDataEncoded = json_encode($pollData);

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
                                            $pollData['name']."
                                        </td>
                                        <td>
                                            $pollType
                                        </td>
                                        <td>".
                                            $pollData['deactDate']."
                                        </td>
                                        <td>";
                                        if(!empty($_SESSION['title'])) {
                                            // Relative path to forms
                                            $ASST_FORM = '../forms/asst.php';
                                            $ADVISORY_VOTE_FORM = "../forms/assoc_full.php";
                                            $REAPPOINTMENT_FORM = "../forms/reappointment.php";
                                            $PROMOTION_FORM = '../forms/promotion.php';
                                            $MERIT_FORM = '../forms/merit.php'; 
                                            $FIFTH_YEAR_REVIEW_FORM = "../forms/quinquennial.php";
                                            // Variables
                                            $redirect = '';
                                            $userTitle = $_SESSION['title'];                
                                            // Logic structure to determine who 
                                            if($pollType == $REAPPOINTMENT) {
                                                if($userTitle == $ASST) {
                                                    if($profTitle == $ASST || $profTitle == $ASSOC) {
                                                        $redirect = $REAPPOINTMENT_FORM;
                                                    } else { $redirect = $ASST_FORM; }
                                                } else { // $userTitle == $ASSOC || $userTitle == $FULL 
                                                    $redirect = $REAPPOINTMENT_FORM;
                                                }
                                            } else if($pollType == $PROMOTION || $pollType == $FIFTH_YEAR_REVIEW) {
                                                if($userTitle == $ASST) {
                                                    if($profTitle == $ASSOC) {
                                                        $redirect = $PROMOTION_FORM;
                                                    } else if($profTitle == $FULL) { 
                                                        $redirect == $ASST_FORM; 
                                                    }
                                                } else if($userTitle == $ASSOC) {
                                                    if($profTitle == $ASSOC) {
                                                        if($pollType == $PROMOTION) {
                                                            $redirect = $PROMOTION_FORM;
                                                        } else if ($pollType == $FIFTH_YEAR_REVIEW) {
                                                            $redirect = $FIFTH_YEAR_REVIEW_FORM;
                                                        }
                                                    } else if($profTitle == $FULL) {
                                                        $redirect = $ASST_FORM;
                                                    }
                                                } else if($userTitle == $FULL) {
                                                    if($profTitle == $ASSOC || $profTitle == $FULL) {
                                                        if($pollType == $PROMOTION) {
                                                            $redirect = $PROMOTION_FORM;
                                                        } else if($pollType == $FIFTH_YEAR_REVIEW) {
                                                            $redirect = $FIFTH_YEAR_REVIEW_FORM;
                                                        }
                                                    }
                                                }
                                            } else if($pollType == $MERIT) {
                                                if($userTitle == $ASST) {
                                                    if($profTitle == $ASST || $profTitle == $ASSOC) {
                                                        $redirect = $MERIT_FORM;
                                                    } else if($profTitle == $FULL) {
                                                        $redirect = $ASST_FORM;
                                                    }
                                                } else if($userTitle == $ASSOC) {
                                                    if($profTitle == $ASST || $profTitle == $ASSOC) {
                                                        $redirect = $MERIT_FORM;
                                                    } else if($profTitle == $FULL) {
                                                        $redirect = $ASST;
                                                    }
                                                } else if($userTitle == $FULL) {
                                                    $redirect = $MERIT_FORM;
                                                }
                                            } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                                                if($userTitle == $ASST) {
                                                    if($profTitle == $ASSOC) {
                                                        $redirect = $FIFTH_YEAR_APPRAISALagi_FORM;
                                                    }
                                                } else if($userTitle == $ASSOC || $userTitle == $FULL) {
                                                    $redirect = $FIFTH_YEAR_APPRAISAL_FORM;
                                                }
                                            } 
                                            /*if($userTitle == $ASST) {
                                                if($pollType == $MERIT) {
                                                    $redirect = '../forms/merit.php';
                                                } else { // Only other form available to Assistant professors 
                                                    $redirect = '../forms/asst.php';
                                                }
                                            } else if($userTitle == $ASSOC || $userTitle == $FULL) {
                                                if($pollType == $MERIT) {
                                                    $redirect = '../forms/merit.php';
                                                } else if($pollType == $PROMOTION) {
                                                    $redirect = '../forms/promotion.php';
                                                    //$redirect = '../forms/assoc_full.php';
                                                } else if($pollType == $REAPPOINTMENT) {
                                                    $redirect = '../forms/reappointment.php';
                                                } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                                                    $redirect = '../forms/fifthYearAppraisal.php';
                                                } else if($pollType == $FIFTH_YEAR_REVIEW) {
                                                    $redirect = '../forms/quinquennial.php';
                                                }
                                            } // End of if( $userTitle == ($ASSOC || $FULL) )
                                            */
                                        } else { // $_SESSION['title'] not set, have user 
                                                // log in to reload data
                                            $msg = "edit.php: error - user title not set.\n";
                                            $msg .= "Redirecting to log in page...";
                                            alertMsg($msg);
                                            signOut();
                                        }
                                        echo"
                                        redirect: $redirect
                                        <form method='post' id='editForm' action='$redirect'>
                                            <button class='btn btn-success'>Edit</button>
                                            <input type='hidden' name='pollData' value='$pollDataEncoded'>
                                        </form>
                                    </td>           
                                </tr>";
                            } // End of while loop
                            // End displaying table
                        } else { // Error executing select cmd 
                            $msg = "edit.php: error when executing selectCmd.\n";
                            $msg .= "Could not display table. Redirecting to home page...";
                            alertMsg($msg);
                            updateAndSaveSession();
                            redirectToHomePage();
                        }
                    } // End of displaying user edit table
                }
            // End of PHP ?>
        </tbody>
    </table>
</div>
</body>
<!-- End of HTML body -->