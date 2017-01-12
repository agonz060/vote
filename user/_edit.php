<?php 
    session_start();
    
    var_dump($_SESSION);

    if(idleTimeLimitReached()) {
        signOut();
    } else { updateLastActivity(); }

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

    function updateAndSave() {
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
        $jsRedirect .= "<script>location.href='index.php'</script>";
        echo $jsRedirect;
        return;
    }
?>
<?php 
    require_once '../event/connDB.php';
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
?>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
    .button-edit {
        color: white;
        background: rgb(28,184,65); 
        width: 80px;
    }
</style>
</head>
<body>
    <table class="pure-table pure-table-bordered" align="center">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Regarding</th>
                <th>Type of Poll</th>
                <th>Poll End Date</th>
                <th>Edit</th>
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
                $poll_id = $title = $description = $endDate  = "";
                $name = $effDate = $pollType = $dept = "";

                $ids = getPollIDs();
                if($ids == -1) {
                    $msg = "edit.php: error executing SELECTCMD in getPollIDs().";
                    $msg .= " Redirecting to user home page...";
                    alertMsg($msg);
                    updateAndSave();
                    redirectToHomePage();
                } else {
                    foreach($ids as $id) {
                        // Only display inactive polls
                        $selectCmd = "SELECT * FROM Polls WHERE CURDATE() <= deactDate ";
                        $selectCmd .= "AND poll_id=$id";
                        $result = mysqli_query($conn,$selectCmd);
                        if($result) { // Get poll data for displaying
                            while($row = $result->fetch_assoc()) {
                                $poll_id = $row["poll_id"];
                                $pollTitle = $row["title"];
                                $description = $row["description"];
                                $endDate = $row["deactDate"];
                                $name=$row["name"];
                                $pollType=$row["pollType"];
                                $dept=$row["dept"];
                                $effDate=$row["effDate"];
                                echo "<tr>
                                        <td>
                                            $pollTitle
                                        </td>
                                        <td>
                                            $description
                                        </td>
                                        <td>
                                            $name
                                        </td>
                                        <td>
                                            $pollType
                                        </td>
                                        <td>
                                            $endDate
                                        </td>
                                        <td>";
                                        if(!empty($_SESSION['title'])) {
                                            // Poll types
                                            $MERRIT = "Merrit";
                                            $PROMOTION = "Promotion";
                                            $REAPPOINTMENT = "Reappointment";
                                            $FIFTH_YEAR_REVIEW = "Fifth Year Review";
                                            $FIFTH_YEAR_APPRAISAL = "Fifth Year Appraisal";

                                            // Var
                                            $redirect = '';
                                            $title = $_SESSION['title'];

                                            if($title == $ASST) {
                                                if($pollType == $MERRIT) {
                                                    $redirect = '../forms/merrit.php';
                                                } else { // Only other form available to Assistant professors 
                                                    //$redirect = '../forms/test.php';
                                                    $redirect = '../forms/asst.php';
                                                }
                                            } else if($title == $ASSOC || $title == $FULL) {
                                                if($pollType == $PROMOTION) {
                                                    $redirect = '../forms/assoc_full.php';
                                                } else if($pollType == $REAPPOINTMENT) {
                                                    $redirect = '../forms/reappointment.php';
                                                } else if($pollType == $FIFTH_YEAR_APPRAISAL) {
                                                    $redirect = '../forms/fifthYearAppraisal.php';
                                                } else if($polltype == $FIFTH_YEAR_REVIEW) {
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
                                        <form method='post' id='editForm' action='$redirect'>
                                            <p id='testingRedirect'><font color='green'><h3>Redirect: $redirect</h3></font></p>
                                            <button class='button-edit pure-button'>Edit</button>
                                            <input type='hidden' name='poll_id' value='$poll_id'>
                                            <input type='hidden' name='profName' value='$name'>
                                            <input type='hidden' name='pollType' value='$pollType'>
                                            <input type='hidden' name='dept' value='$dept'>
                                            <input type='hidden' name='effDate' value='$effDate'>
                                            <input type='hidden' name='deactDate' value='$endDate'>
                                        </form>
                                    </td>           
                                </tr>";
                            } // End of while loop
                        } else { // Error executing select cmd 
                            $msg = "edit.php: error when executing selectCmd.\n";
                            $msg .= "Could not display table. Redirecting to home page...";
                            alertMsg($msg);
                            updateAndSave();
                            redirectToHomePage();
                        }
                    } // End of displaying user edit table
                }
            // End of PHP ?>
        </tbody>
    </table>
</body>
<!-- End of HTML body -->