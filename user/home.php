<?php 
    session_start();
    //var_dump($_SESSION);
    timeSinceLastActivity();

    if(idleLimitReached()) {
        signOut();
    } else {
        unsetPollVariables();
        updateLAstActivity();
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

    function timeSinceLastActivity() {
        $t = time() - $_SESSION['LAST_ACTIVITY'];
        //echo "Time since last activity: $t";
        return;
    }
    
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
        $REAL_PATH = "REAL_PATH_DOC_ROOT";

        unset($GLOBALS['_SESSION'][$PROF_NAME]);
        unset($GLOBALS['_SESSION'][$DESCRIPTION]);
        unset($GLOBALS['_SESSION'][$CMT]);
        unset($GLOBALS['_SESSION'][$POLL_ID]);
        unset($GLOBALS['_SESSION'][$POLL_TYPE]);
        unset($GLOBALS['_SESSION'][$PROF_NAME]);
        unset($GLOBALS['_SESSION'][$DEACT_DATE]);
        unset($GLOBALS['_SESSION'][$READ_ONLY]);
        unset($GLOBALS['_SESSION'][$DEPT]);
        unset($GLOBALS['_SESSION'][$REAL_PATH]);
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
/* End of session verification*/
?>
<?php 
    require_once '../event/connDB.php';
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['action'])) {
                $EDIT = "edit";
                $REVIEW = "review";
                $SIGNOUT = "signOut";

            if(!idleLimitReached()) {
                $action = $_POST['action'];
                if($action == $EDIT) {
                        updateLastActivity();
                        saveSessionVars();
                        redirectToEditPage();      
                } else if($action == $REVIEW) {
                        updateLastActivity();
                        saveSessionVars();
                        redirectToReviewPage();
                } else if($action == $SIGNOUT) {
                        signOut();
                        saveSessionVars();
                        redirectToLogIn();
                } 
            } // End of isValidSession()
        } // End of $_POST['action']
    } // End of $_SERVER['REQUEST_METHOD']
    
    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='edit.php'</script>;";
        echo $jsRedirect;
        return;
    }

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='review.php'</script>;";
        echo $jsRedirect;
        return;
    }

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>;";
        echo $jsRedirect;
        return;
    }
?>
<?php
    function getOutstandingVotes($conn, $user_id) {
    	    $outstandingVotes = 0;
	    $query = "SELECT count(user_id) AS COUNT FROM Voters WHERE user_id=?";
	    $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
	    mysqli_stmt_bind_param($stmt, "s", $user_id) or die(mysqli_error($conn));
	    mysqli_stmt_execute($stmt) or die($conn->error) or die(mysqli_error($conn));
	    mysqli_stmt_bind_result($stmt, $outstandingVotes) or die(mysqli_error($conn));
	    mysqli_stmt_fetch($stmt);
	    return $outstandingVotes;
    }
    $outstandingVotes = getOutstandingVotes($conn, $_SESSION['user_id']);	    
?>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .navbar {
	margin-bottom: 0px;
    }
    div.jumbotron {
    	background-image: url('http://newsroom.ucr.edu/images/releases/2787_0hi.jpg');
	background-size: cover;
	color: white;
        background-repeat: no-repeat;
        text-shadow: black 1px 1px 1px;
	min-height: 100vh;
	text-align: center;
	margin-bottom: 0;
    }
    .btn-transparent {
    	background-color: transparent;
	border-color: white;
    }
</style>
</head>
<body>
<!-- Display webpage title -->
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="active"><a href="home.php">Home</a></li>
			<li><a href="edit.php">Edit Poll</a></l>
			<li><a href="review.php">Review Poll</a></li>
		</ul>
	</div>
</nav>
<div class="jumbotron">
	<h1>Welcome <?php echo htmlspecialchars($_SESSION["userName"]); ?>!</h1>
	<p>You have <?php echo $outstandingVotes; ?>  polls to vote on.</p>
	<form  method="POST" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	<div id="menu" name="menu" align="center">
	    <button name="action" value="signOut" class="btn btn-transparent">Sign out</button>
	</div>
	</form>
		
</div>
<!-- Web page HTML ends here -->
<!-- JS begins here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>    
<script>
    $(document).ready(function() {
        // Set interval to reload page for user authentication purposes
        setInterval(reloadPage,1200000); //1200000 ms = 1200 s = 20 mins
    });

    function reloadPage() {
        location.reload();
    };
</script>
<!-- JS ends here -->
</body>
