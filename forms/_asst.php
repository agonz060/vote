<?php 
    session_start();
    //var_dump($_SESSION);
    
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

// End session verification  
?>
<?php
    require_once '../event/connDB.php';
    //echo "1";
    // Poll data
    // * NOTE: $_voteData is data posted from a database(storage)
    $pollData = $_voteData = array();
    $name = $deactDate = $pollType = "";
    $alertMsg = $dataErrorMsg = "";

    // Get all posted poll data
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        print_r($_POST);
        // Security and poll checks
        if(idleTimeLimitReached()) {
            signOut();
        } else if(!empty($pollData)) {
            $d = $pollData['deactDate'];
            if(isPollExpired($d)) {
                cancelVote();
            }
        } // End of else if(...)
    
        if(!empty($_POST['action'])) {
            $CANCEL = "Cancel";

            $cmt = "";
            $action = $_POST['action'];

            if($action == $CANCEL) {
                cancelVote();
            }         
        } else { // Get poll data posted by /user/edit.php 
            //echo "Loading poll data<br>";
            if(!empty($_POST['pollData'])) {
                //echo "pollId set\n";
                $pollData = $_POST['pollData'];
                $pollData = json_decode($pollData,true);
                //$_SESSION['poll_id'] = cleanInput($_POST['poll_id']);
            } else { // Error 
                $alertMsg = "asst.php: error loading pollData";
                alertAndRedirect($alertMsg);
            }

            if(!empty($_POST['_voteData'])) {
                $_voteData = $_POST['_voteData'];
                $_voteData = json_decode($_voteData,true);     
            } 
            //print_r($_SESSION);
            //echo "2";
        } // End !empty($_POST['action']) else block
        
        //updateLastActivity();
    } // End of $_SERVER['REQUEST_METHOD']

    function cancelVote() {
        updateAndSaveSession();
        redirectToEditPage();
    }

    function alertAndRedirect($msg) {
        alertMsg("$msg");
        updateAndSaveSession();
        redirectToEditPage();
        return;
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../user/edit.php';</script>";
        echo $jsRedirect;
        return;
    }

    function alertMsg($msg) {
        $jsAlert = "<script type='text/javascript' ";
        $jsAlert .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsAlert .= "<script>alert('$msg');</script>";
        echo $jsAlert;
        return;
    }

    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
<head>
<title>Faculty Confidential Advisory Vote To The Chair</title>
<style>
	.preface {
		color: #3333ff;
	}
</style>
</head>
<body>
<form style="width:70%; margin: 0 auto" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
<h1 align="center"><u>Confidential Document</u></h1>
<p id="dataErrorMsg" align="center"><?php echo $dataErrorMsg; ?></p>
<h4 align="center">*NON VOTING FACULTY MEMBERS</h4>
<h4 align="center">CONFIDENTIAL EVALUATION FORM</h4>
<div>
    <p class="preface">
        <strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
        faculty member wishes to remain anonymous and/or will not be able to attend the meeting 
        and would like the comments brought up at the meeting for discussion.<br>
    </p>
    <p class="preface">
        [Use this statement for ECE and CEE confidential evals instead of the above <strong>NOTE..:</strong>
        Comment may be submitted to the chair prior to the department meeting if the faculty member will
        not be able to attend the meeting and would like the comments brought up at the meeting for discussion.]<br>
    </p>

    <p class="preface">
        Comments not discussed at the meeting will not be reflected in the department letter.
    </p>
</div>
<hr/>
<div>
    <p> 
        Merit, Promotions, or Fith-year appraisal for 
        <?php if(!empty($pollData)) {
                echo $pollData['name']."'s ".$pollData['pollType'].'.';
                } ?> 
    </p>
    Confidential comments regarding Merrit, Promotions or Fifth-year appraisals.</br>
    <textarea name="comment" id="comment" rows="8" style="width:100%"><?php 
            // PHP open tag above
            if(!empty($_voteData['voteCmt'])) { echo $_voteData['voteCmt']; } 
        ?></textarea>
</div>
<hr/>
<p> Comments must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the 
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<div id="actionDiv">
    <input type="submit" name="action" value="Cancel">
    <?php if(empty($pollData['READ_ONLY'])) {
            $displaySubmitButton = "<button type='button' id='submitButton'>Submit</button>";
            echo $displaySubmitButton;
        }
    ?>
</div>
</form>
<!-- End form -->
<!-- Scripting begins -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>

    // Document ready
    $(function() {
    	$( "#effDate" ).datepicker( { dateFormat: 'yy-mm-dd' } );

        $("#submitButton").click(function() {
            submitVote();
        })
    });
    // Helper functions begin here
    function submitVote() {
        var _comment = $("#comment").val();
        var userVoteData = { comment: _comment };
        if(_comment.length == 0 || !_comment.trim()) {
            var noCommentEntered = "Vote must contain comment to submit";
            $("#dataErrorMsg").val(noCommentEntered);
        } else {
            var _pollData = <?php if(!empty($pollData)) { echo json_encode($pollData); } else  { echo 0; } ?>;
            if(_pollData) { 
                $.post("../user/submitVote.php", { voteData: userVoteData, pollData: _pollData }
                        , function(data) { 
                            if(data) { // Error occured during submission
                                alert(data);
                            } else { // Successful submission
                                alert("Thank you for voting!");
                                window.location.href = "../user/edit.php";
                            } 
                        }) // End of function()
                    .fail(function() {
                        var msg = "asst.php : error posting to submitVote.php";
                        alert(msg);
                }); // End of $.post(...)
            } // End of if(...)
        } // End of else(...)
    }
</script>
</body>