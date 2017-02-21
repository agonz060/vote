<?php 
    session_start();
    
    //var_dump($_SESSION);
    //echo 'one';
    if(idleLimitReached()) {
        signOut();
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

    function signOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();

        // Save and redirect
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
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
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
<!-- Display webpage title -->
<h1 align="center"> User Homepage </h1>
<hr>
<form  method="POST" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<div id="menu" name="menu" align="center">
    <button name="action" value="edit" class="button-edit pure-button">View current ballots</button> 
    <button name="action" value="review" class="button-review pure-button">Review past ballots</button>
    <button name="action" value="signOut" class="button-signOut pure-button">Sign out</button>
</div>
</form>
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