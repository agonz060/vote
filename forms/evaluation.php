<?php
    session_start();
    //var_dump($_SESSION);

    /*if(idleTimeLimitReached()) {
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
*/
    function signOut() {
        // Destroy previous session
        /*session_unset();
        session_destroy();

        // Begin new session
        session_regenerate_id(true);
        session_start();
        saveSessionVars();*/

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
    require_once '../includes/connDB.php';
    require_once '../includes/functions.php';
    // Poll data
    // * NOTE: $_voteData is data posted from a database(storage)
    $pollData = $voteData = array();
    $name = $deactDate = $pollType = "";
    $alertMsg = $dataErrorMsg = "";

    // Get all posted poll data
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // print_r($_POST);
        // if(!empty($_POST['action'])) {
        //     $CANCEL = "Cancel";

        //     $cmt = "";
        //     $action = $_POST['action'];

        //     if($action == $CANCEL) {
        //         redirectToEditPage();
        //     }
        // } else { // Get poll data posted by /user/edit.php

            //echo "Loading poll data<br>";
            if(isset($_POST['pollData'])) {
                // print "Post voteData: "; print_r($_POST['pollData']); print "<hr>";
                $pollData = $_POST['pollData'];
                $pollData = json_decode($pollData,true);
                // print "decoded voteData: "; print_r($pollData); print "<hr>";
                // print_r($pollData);
                //$_SESSION['poll_id'] = cleanInput($_POST['poll_id']);
            } else { // Error
                $alertMsg = "advisoryComment.php: error loading pollData";
                alertAndRedirect($alertMsg);
            }

            if(isset($_POST['voteData'])) {
                // echo "Post voteData: "; print_r($_POST['voteData']); print "<hr>";
                $voteData = $_POST['voteData'];
                $voteData = json_decode($voteData,true);
                // print "<br>voteData: "; print_r($voteData);
                // print_r($_voteData);
            }
            //print_r($_SESSION);
            //echo "2";
        // } // End !empty($_POST['action']) else block

        //updateLastActivity();
    } // End of $_SERVER['REQUEST_METHOD']

    function cancelVote($p) {
        print_r($p);
        // updateAndSaveSession();
        // if(empty($p['READ_ONLY'])) {
        //     redirectToReviewPage();
        // } else {
        //     redirectToEditPage();
        // }
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

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../user/review.php';</script>";
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
        <strong>NOTE:&nbsp</strong>
        <?php
            $notices = getNotices();
            echo $notices[$pollData['notice']];
        ?>
        <br>
    </p>
    <p class="preface">
        Comments not discussed at the meeting will not be reflected in the department letter.
    </p>
</div>
<hr/>
<div>
    <?php
        $pOpen = '<p>';
        $pClose = '</p>';
        $OTHER = "Other";
        $pollType = $pollData['pollType'];
        $profName = $pollData['name'];

        // Display description
        if($pollType == $OTHER) {
            $pollType = $pollData['otherPollTypeInput'];
        }
        $description = "$pollType for $profName".".";
        echo $pOpen . $description . $pClose;

        $EVALUATION = 3;
    	$actionInfo = array();
    	$fromTitle = $fromStep = $toTitle = $toStep = $actionNum = "";
        if(isset($pollData)) {
            // echo "Poll data is set <br>";
        	if(isset($pollData['actionInfoArray'])) {
                $actionInfo = $pollData['actionInfoArray'];
                // print "actionInfo: "; print_r($actionInfo); print "<hr>";
                // echo "actionInfoArray is set <br>"; print_r($actionInfo); echo "<br>";
                if($pollData['numActions'] > 1) {
                    if($pollData['userTitle'] == 'Assistant Professor' && $pollData['assistantForm'] == $EVALUATION) {
                        $actionNum = $pollData['assistantEvaluationNum'];
                        // print "Assistant action: $actionNum <br>";
                    } else if($pollData['userTitle'] == 'Associate Professor' && $pollData['associateForm'] == $EVALUATION) {
                        $actionNum = $pollData['associateEvaluationNum'];
                        // print "Associate action: $actionNum <br>";
                    } else if($pollData['userTitle'] == 'Full Professor' && $pollData['fullForm'] == $EVALUATION) {
                        $actionNum = $pollData['fullEvaluationNum'];
                        // print 'Full action: $actionNum <br>';
                    }
                    $actionInfo = $actionInfo[$actionNum];
                    // print "userTitle: ". $pollData['userTitle'] . "actionInfo for action: $actionNum <br>"; print_r($actionInfo); print "<hr>";
                }
                // Extract action information to display to user
                $fromTitle = $actionInfo['fromTitle'];
                $fromStep = $actionInfo['fromStep'];
                $toTitle = $actionInfo['toTitle'];
                $toStep = $actionInfo['toStep'];
                // Form action description for displaying
        		echo "Confidential comments regarding $profName"."'s $pollType $fromTitle $fromStep to $toTitle $toStep.";
        	} else {
                echo "Confidential comments regarding $profName's $pollType.";
            }
        } else {
        	echo "Confidential comments regarding $profName's $pollType.";
        }
    ?><br><br>
    <textarea name="comment" id="comment" rows="8" style="width:100%"><?php
            // PHP open tag above
            if(isset($voteData['voteCmt'])) { print $voteData['voteCmt']; }
        ?></textarea>
</div>
<!-- <div>
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
</div>-->
<hr/>
<p> Comments must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<div id="actionDiv">
    <button type="button" id="Cancel">Cancel</button>
    <!-- <input type="submit" name="action" value="Cancel"> -->
    <?php if(empty($pollData['READ_ONLY'])) {
            $displaySubmitButton = "<button type='button' id='submitButton'>Submit</button>";
            echo $displaySubmitButton;
        }
    ?>
</div>
</form>
</body>
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
        });
        $("#Cancel").click(function() {
            var readOnly = <?php if(isset($pollData['READ_ONLY'])) { echo 1; } else { echo 0; } ?>;
            if(readOnly) {
                window.location.href = "../user/review.php";
            } else {
                window.location.href = "../user/edit.php";
            }

        });
    });
    // Helper functions begin here
    function submitVote() {
        var _comment = $("#comment").val();
        var userVoteData = { voteCmt: _comment };
        console.log(userVoteData);
        // if(_comment.length == 0 || !_comment.trim()) {
        //     var noCommentEntered = "Vote must contain comment to submit";
        //     $("#dataErrorMsg").val(noCommentEntered);
        // } else {
            var _pollData = <?php if(!empty($pollData)) { echo json_encode($pollData); } else  { echo 0; } ?>;
            console.log(_pollData);
            //_pollData = 0;
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
        // } // End of else(...)
    } // End of submitVote()
</script>