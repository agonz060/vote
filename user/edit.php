<?php 
    require_once '../event/connDB.php';
    session_start();
    //var_dump($_SESSION);

    if(!isValidSession()) {
        signOut();
        saveSessionVars();
        redirectToLogIn();
    }

    /*if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['action'])) {
                $EDIT = "edit";
                $REVIEW = "review";
                $SIGNOUT = "signOut";

                if(isValidSession()) {
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
    */

    function isValidSession() {
            if(!(empty($_SESSION['LAST_ACTIVITY']))) {
                if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
                    if(isSessionExpired()) {
                        return 0;
                    } else { return 1; }
                } else { // Error must have occurred
                        return 0; }
            } else { // Error must have occurred 
                return 0; }
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
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='edit.php'</script>";
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
?>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
    .button-edit {
        color: white;
        background: rgb(28,184,65); 
        width: 80px;
    }
    .button-delete {
        color: white;
        background: rgb(202,60,60);
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
                <th>Ballot End Date</th>
                <th>Edit/Submit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Poll data
                $poll_id = $title = $description = $endDate  = "";
                $name = $effDate = $pollType = $dept = "";

                // Only display inactive polls (polls that have a start date > current date) 
                $selectCmd="Select * from Polls Where deactDate > CURDATE()";
                $result = $conn->query($selectCmd);
                
                // Get poll data for displaying
                while($row = $result->fetch_assoc()) {
                    $poll_id = $row["poll_id"];
                    $title = $row["title"];
                    $description = $row["description"];
                    $endDate = $row["deactDate"];
                    $name=$row["name"];
                    $pollType=$row["pollType"];
                    $dept=$row["dept"];
                    $effDate=$row["effDate"];
                    echo "<tr>
                            <td>
                                $title
                            </td>
                            <td>
                                $description
                            </td>
                            <td>
                                $actDate
                            </td>
                            <td>
                                $deactDate
                            </td>
                            <td>
                                $dateModified
                            </td>
                            <td>
                                <form method='post' id='editForm' action='../event/vote.php'>
                                    <button class='button-edit pure-button' name='poll_id' value='$poll_id'>Edit</button>
                                    <input type='hidden' name='title' value='$title'>
                                    <input type='hidden' name='description' value='$description'>
                                    <input type='hidden' name='dateActive' value='$actDate'>
                                    <input type='hidden' name='dateDeactive' value='$deactDate'>
                                    <input type='hidden' name='profName' value='$name'>
                                    <input type='hidden' name='pollType' value='$pollType'>
                                    <input type='hidden' name='dept' value='$dept'>
                                    <input type='hidden' name='effDate' value='$effDate'>
                                </form>
                                <button class='button-delete pure-button' value='$poll_id'>Delete</button>  
                            </td>           
                        </tr>";
                }
            ?>
        </tbody>
    </table>
</body>
<!-- End of web page HTML -->