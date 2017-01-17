<?php 
    session_start();

    /*if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
    */

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

    function isAdmin() {
        if(!empty($_SESSION['title'])) {
            $ADMIN = "Administrator";

            if($_SESSION['title'] !== $ADMIN) {
                return 0;
            } else return 1;
        }
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
    
    function redirectToAddPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='add.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToVotePage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='vote.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='edit/editTable.php'</script>";
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

    function redirectToLogIn() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../index.php'</script>";
        echo $jsRedirect;
        return;
    }
    
/* Session verification ends here */ 
?>

<?php
    require_once 'event/connDB.php';
     /* $_SERVER(POST) starts here */
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['action'])) {
            // Menu options
            $ADD = "add";
            $EDIT = "edit";
            $CREATE = "create";
            $REVIEW = "review";
            $SIGNOUT = "signOut";

            if(!idleLimitReached()) {
                $action = $_POST['action'];
                if($action == $ADD) {
                    updateAndSave();
                    redirectToAddPage();
                } else if($action == $CREATE) {
                    updateAndSave();
                    redirectToVotePage();
                } else if($action == $EDIT) {
                    updateAndSave();
                    redirectToEditPage();      
                } else if($action == $REVIEW) {
                    updateAndSave();
                    redirectToReviewPage();
                } else if($action == $SIGNOUT) {
                    signOut();
                    saveSessionVars();
                    redirectToLogIn();
                } 
            } else { // End of isValidSession()
                signOut();
            } // End of else
        } 
    } // End of $_SERVER['REQUEST_METHOD']

    function updateAndSave() {
        updateLastActivity();
        saveSessionVars();
    }
/* $_SERVER(POST) ends here */
?>
<body>
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
        background: rgb(202,60,60);
    	width: 160px;
	}
	.button-create {
        text-align: center;
		color: white;
		background: rgb(66,184,221);
		width: 160px;
	}
    .button-add {
        text-align: center;
        color: white;
        background: rgb(255,140,0);
        width: 160px;
    }
    .button-signOut {
        text-align: center;
        color: white;
        background: rgb(102, 153, 153);
        width: 160px;
    }
</style>
</head>
<!-- Display webpage title -->
<h1 align="center">BCOE Voting Management</h1>
<hr>
<!-- Display menu options -->
<div id="menuButtons" align="center" >
    <form id="menuForm" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <button name="action" value="create" class="button-create pure-button">Create Poll</button> 
    <button name="action" value="edit" class="button-edit pure-button">Edit Poll</button>
    <button name="action" value="review" class="button-review pure-button">Review results</button>
    <button name="action" value="add" class="button-add pure-button">Add User</button>
    <button name="action" value="signOut" class="button-signOut pure-button">Sign Out</button>
    </form>
</div> <!-- End displaying menu buttons -->
<!-- Scripting begins -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>    
<script>
        $(document).ready(function() {
            // Set interval to reload page for user authentication purposes
            setInterval(reloadPage,1200000); //1200000 ms = 1200 s = 20 mins
        });

        function reloadPage() {
                location.reload();
        };                 
</script> <!-- Scripting ends here -->
</body> <!-- Document body ends here -->
