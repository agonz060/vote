<?php 
    session_start();
    var_dump($_SESSION);

    if(isValidSession()) {
        echo 'Session valid';
        echo 'Updating last activity';
        updateLastActivity();
        //var_dump($_SESSION);
        saveSessionVars();
        return 1;
    } else { // Idle time limit exceeded
            // update LAST_ACTIVITY
        return 0; 
    }

    function isValidSession() {
            if(!(empty($_SESSION['LAST_ACTIVITY']))) {
                if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
                    if(isSessionExpired()) {
                        return 0;
                    } else { return 1; }
                } else { // Error must have occurred
                        return 0;
                }
            } else { // Error must have occurred 
                return 0; 
            }
    } // End of checkIdleTime() 

    function isSessionExpired() {
        $lastActivity = $_SESSION['LAST_ACTIVITY'];
        $timeOut = $_SESSION['IDLE_TIME_LIMIT'];
        $elapsedTime = time() - $lastActivity;
        echo "Elapsed time: $elapsedTime";

        // Check if session has been active longer than IDLE_TIME_LIMIT
        if($elapsedTime >= $timeOut) {
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
?>