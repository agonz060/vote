<?php 
    session_start();
    //var_dump($_SESSION);
    
    /*if(idleTimeLimitReached()) {
        signOut();
    } else { updateLastActivity(); }
    */
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
    // Form data 
    // * NOTE: $_voteData is user data previously submitted by the user
    //                     sent from review.php
    $pollData = $_voteData = "";
    $name = $pollType = $profTitle = $dept = $effDate = "";
    // Vote data 
    $comment = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //print_r($_POST);
        //echo "here";
        if(!empty($_POST['cancelVote'])) {
            echo "canceling vote";
            cancelVote();
        }

        if(!empty($_POST['pollData'])) {
            $pollData = $_POST['pollData'];
            $pollData = json_decode($pollData,true);

            $name = $pollData['name'];
            $pollType = $pollData['pollType'];
            $dept = $pollData['dept'];
            $effDate = $pollData['effDate'];
        } else { // Error  
            $alertMsg = "reappointment.php: error loading pollData";
            alertAndRedirect($alertMsg);
        }

        if(!empty($_POST['_voteData'])) {
            $_voteData = $_POST['_voteData'];
            $_voteData = json_decode($_voteData,true);  
        } 
    } // End of if($_SERVER[...])
    
    function cancelVote() {
        updateAndSaveSession();
        redirectToReviewPage();
    }

    function alertAndRedirect($msg) {
        alertMsg("$msg");
        updateAndSaveSession();
        redirectToEditPage();
        return;
    }

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript' ";
        $jsRedirect .= "src='http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js'></script>";
        $jsRedirect .= "<script>location.href='../user/review.php';</script>";
        echo $jsRedirect;
        return;
    }
// End of PHP 
?>
<html>
<head>
<title>Faculty Confidential Vote To The Chair</title>
<style>
	.preface {
		color: #3333ff;
	}
</style>
</head>
<body>
<form style="width:70%; margin: 0 auto" method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2 align="center">Faculty Confidential Vote To The Chairman</h2>
<div>
<p class="preface">
<strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
faculty member wishes to remain anonymous and/or will not be able to attend the meeting 
and would like the comments brought up at the meeting for discussion.<br>
</p>
<p class="preface">
[Use this statement for CEE ballots instead of the above <strong>NOTE..:</strong>
Comment may be submitted to the chair prior to the department meeting if the faculty member will
not be able to attend the meeting and would like the comments brought up at the meeting for discussion.]<br>
</p>
<p class="preface">
(Use this statement EE ballots: Anonymous or absentee comments will be raised at the
meeting at the Chair's discretion. .....This is in addition to the above
<strong>NOTE:</strong> Comments may be submitted....)<br>
</p>
<p class="preface">
Comments not discussed at the meeting will not be reflected in the department letter.
</p>
</div>
<hr/>
<div>
<p> 
I cast my vote regarding the recomendation for Professor
<?php
    $displayStr = "$name's Reappointment in the Department of $dept ";
    $displayStr .= ", effective $effDate.<br>";  
    echo $displayStr;
// End PHP ?><br>
<i>Please note that a vote against reappointment may result in the Chancellor's final decision not to
reappoint with a terminal notice being given to the candidate.</i></br>
</p>
In Favor: <input type="radio" name="vote" value="1">&nbsp;&nbsp;&nbsp;  
Opposed: <input type="radio" name="vote" value="2">&nbsp;&nbsp;&nbsp;
Abstain: <input type="radio" name="vote" value="3"></br>
<hr/>
Comments:</br>
<textarea id="voteCmt" rows="8" style="width:100%"><?php 
        if(!empty($_voteData['voteCmt'])) { echo $_voteData['voteCmt']; } ?></textarea>
</div>
<hr/>
<p> Ballots must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the 
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<p>
<input type="submit" name="cancelVote" value="Cancel">
<?php if(empty($pollData['READ_ONLY'])) {
            $displaySubmitButton = "<button type='button' id='submitButton'>Submit</button>";
            echo $displaySubmitButton;
        }
    ?>
</p>
</form>
</body>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
// Load vote data from server if available
loadVoteData();

$(function() { // Document ready
    $("#submitButton").click(function() { 
        //getVoteData();
        submitVote(); 
    }); // End submitButton action
    $( "#effDate" ).datepicker( { dateFormat: 'yy-mm-dd' } );
}); // End JQuery document ready

function loadVoteData() {
    var loadData = <?php if(!empty($pollData['READ_ONLY'])) { echo 1; }
                            else { echo 0; } ?>;
    if(loadData) {  // Load and display user data from server     
        var vote = <?php if(!empty($_voteData['vote'])) 
                            { echo $_voteData['vote']; } 
                            else { echo 0; } ?>;
        $('input[name=vote][value='+vote+']').attr('checked','checked');
    }  
}

function getVoteData() {
    var IN_FAVOR_VOTE = 2;
    var _vote = $("input[name=vote]:checked").val();
    var _voteCmt = $("#voteCmt").val();
    //alert("vote:"+_vote+" _comments:"+_comments);

    if(_vote) {
        if(_voteCmt.length == 0 || !_voteCmt.trim()) {
            var noCmtEntered = "Comment(s) required to submit vote.";
            alert(noCmtEntered);
            return 0;
        } 

        if(_vote > 0 && _vote <= 3) {
            var voteData = { vote: _vote, voteCmt: _voteCmt };
            /*
            var voteDataStr = "vote: "+_vote;
                voteDataStr += "voteCmt: " + _voteCmt; 
            alert(voteDataStr);
            */
            return voteData;
        }
    } else { // Vote missing 
        var voteMissing = "Please select a voting option before submitting.";
        alert(voteMissing);
        return 0;
    }
}
function submitVote() {
        var isReadOnly = <?php if(!empty($pollData['READ_ONLY'])) { echo 1; }
                                else { echo 0; } ?>;
        if(!isReadOnly) {
        var userVoteData = getVoteData();
        if(userVoteData) {
            //alert(userVoteData);
            var _pollData = <?php if(!empty($pollData)) { echo json_encode($pollData); } else {echo 0;} ?>;
            //alert(_pollData);
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
            }
        } else { // Error while getting vote data
            alert("Something went wrong while collecting vote information.");
        }
        } // End of if(!isReadOnly)   
} // End of submitVote()
</script>
</html>