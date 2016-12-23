<?php 
    require_once '../event/connDB.php';
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
    } else { // Error must have occurred 
        invalidCredentials(); 
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
        header("Location: ../index.php");
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
                background: rgb(202,60,60);
        	width: 160px;
	}
</style>
</head>
<body>
<!-- Display webpage title -->
<h1 align="center"> User Homepage </h1>
<hr>
<form action="user/event/edit.php">
<button class="button-edit pure-button">View current ballots</button> 
</form>
<form action="user/event/review.php">
<button class="button-review pure-button">Review past ballots</button>

<!-- JS begins here -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>    
<script>
        function timeTest() {
                location.reload(true);
        };              

        $(document).ready(function() {
                setTimeout(timeTest,1200000); // 1200000ms = 20 mins
        });
</script>
<!-- JS ends here -->
</body>