<?php 
    require_once 'event/connDB.php';
    session_start();

    // Redirect user to correct page if already logged in
    // and cookie is still valid
    if(!(empty($_SESSION['LAST_ACTIVITY']))) {
        if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
            if(isSessionExpired()) {
                logOut();
                redirectUserToLogin();
            } 
        } else { // Error must have occurred 
                invalidCredentials();
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

    function invalidCredentials() {
        logOut();
        redirectUserToLogin();
    }

    function logOut() {
        // Destroy previous session
        session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();
        return;
    }

    function redirectUserToLogin() {
        header("Location: ../../index.php");
    }
        
?>
<body>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
        .button-edit {
                color: white;
                background: rgb(28,184,65);
        	width: 80px;
	}
        .button-review {
                color: white;
                background: rgb(202,60,60);
        	width: 80px;
	}
	.button-create {
		color: white;
		background: rgb(66,184,221);
		width: 80px;
	}
        .button-add {
                color: white;
                background: rgb(255,140,0);
                width: 80px;
        }
</style>
</head>
<!-- Display webpage title -->
<h1 align="center"> BCOE Voting Management</h1>
<hr>
<form action="event/vote.php">
<button class="button-create pure-button">Create</button> 
</form>
<form action="edit/editTable.php">
<button class="button-edit pure-button">Edit</button>
</form>
<form action="event/vote.php">
<button class="button-review pure-button">Review</button>
</form>
<form action="event/addUser.php">
<button class="button-add pure-button">Add</button>
</form>
</body>