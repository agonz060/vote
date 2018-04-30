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

    // Form data 
    // * NOTE: $_voteData is user data previously submitted by the user
    //                     sent from review.php
    $pollData = $_voteData = "";
    $name = $pollType = $profTitle = $dept = $effDate = "";
    $READ_ONLY = "";
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
            $profTitle = $pollData['profTitle'];
            $dept = $pollData['dept'];
            $effDate = $pollData['effDate'];
            $numActions = $pollData['numActions'];

            if(isset($pollData['actionInfoArray'])) {
                $actionInfoArray = $pollData['actionInfoArray'];
                //print("ActionInfo: "); print_r($actionInfoArray);
            }
        } else { // Error  
            $alertMsg = "merit.php: error loading pollData";
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
    function intToRoman($num) {
        if($num=="") {
            return "";
        }
        $romanArr = array();
        $romanArr[1] = "I"; $romanArr[2] = "II"; $romanArr[3]= "III";
        $romanArr[4] = "IV"; $romanArr[5] = "V"; $romanArr[6]= "VI";
        $romanArr[7] = "VII"; $romanArr[8] = "VII"; $romanArr[9] = "IX";
        $romanArr[10] = "X";
        return $romanArr[$num];
    }
// End of PHP 
?> 
<html>
<head>
<title>Faculty Confidential Advisory Vote To The Chair</title>
<style>
	.preface {
		color: #3333ff;
	}
</style>
</head>
<body>
<form style="width:70%; margin: 0 auto" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2 align="center">Faculty Confidential Vote To The Chairman</h2>
<p id="dataErrorMsg" align="center"></p>
<div>
<p class="preface">
<strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
faculty member wishes to remain anonymous and/or will not be able to attend the meeting 
and would like the comments brought up at the meeting for discussion.<br>
</p>
<p class="preface">
Anonymous or absentee comments will be raised at the meeting at the Chair's discretion.
</p>
<p class="preface">
Comments not discussed at the meeting will not be reflected in the department letter.
</p>
</div>
<hr>
<!-- Start voting options -->
<?php // Displaying voting options in PHP  
    // Voting options are broken up into HTML strings stored in variables
    // to be display appropiate actions (i.e if a vote has 2 actions, then
    // $action = $intro, $step, $outro, $voteOptions, $completeVoteComment will  
    // be displayed twice)
    $displayActions = array();
    // 
    if($numActions > 0 && $numActions <= 3) {
        $actionCount = 0;
        $actionInfo = "";

        while($actionCount < $numActions) {
            $actionNum = $actionCount + 1;
            $voteNum = "vote".$actionNum;
            $index = $actionCount;

            if($index < $numActions) {
                $actionInfo = $actionInfoArray[$index];
            }
            // Default intro
            $intro = "<div>\n<p>\nI cast my vote regarding the recomendation for $name's $pollType Advancement from Professor, ";
            if($actionInfo['accelerated']) {
                // Accelerted intro
                $intro = "<div>\n<p>\nI cast my vote regarding the recomendation for $name's Accelerated $pollType Advancement from $action, ";
            }
            // Setup var's 
            $fromLevel = intToRoman($actionInfo['fromLevel']);
            $toLevel = intToRoman($actionInfo['toLevel']);
            // Insert variables 
            $step = "Step ".$fromLevel." (OS) to Professor, Step ".$toLevel;
            $outro = " in the Department of $dept, effective $effDate <br>\n</p>";
            // Setup vote options using action number
            $voteOptions = "In Favor: <input type='radio' name='".$voteNum."' value='1'>&nbsp;&nbsp;&nbsp;  
                            Opposed: <input type='radio' name='".$voteNum."' value='2'>&nbsp;&nbsp;&nbsp;
                            Abstain: <input type='radio' name='".$voteNum."' value='3'><br>\n<hr>";
            $startVoteComment = "Comments:<br>
                                <textarea id= 'voteCmt".$actionNum."' rows='8' style='width:100%'>";
            $endVoteComment = "</textarea>\n</div>\n<hr>";
            // Combine comment parts
            $completeVoteComment = $startVoteComment . $endVoteComment;
            // Tie everything together
            $action = $intro . $step . $outro . $voteOptions . $completeVoteComment;
            // Add action to action array
            $displayActions[] = $action;
            $actionCount += 1;
        } // End of while
        // Display actions
        $displayCount = 0;
        while($displayCount < $numActions) {
            echo $displayActions[$displayCount];
            $displayCount += 1;
        } 
    } // End of Displaying Actions
 // End dipslaying voting options in PHP ?>
<!-- End voting options -->
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
// End PHP ?>
</p>
</form>
</body>
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script>
    // Document ready
    $(function() {
        $("#submitButton").click(function() {
            submitVote();
        });
        loadVoteData();
    });

    function loadVoteData() {
        var ERROR = -1;
        var VOTE = "vote";
        var VOTECMT = "voteCmt";
        var displayCount = 0;
        var numActions = 0;
        var index = 0;
        var actionNum = 0;
        var vote = 0;
        var voteCmt = "";
        var voteSeletor = "";
        var voteCmtSelector = "";
        var voteData = [];
        var loadData = <?php if(empty($pollData['READ_ONLY'])) { echo 0; }
                                else { echo 1; } ?>;
        var voteDataArray = <?php if(isset($_voteData)) {
                                echo json_encode($_voteData);
                            } else { echo 0; }?>;
        //console.log(voteData);
        if(loadData && voteData) { 
            numActions = <?php if(isset($numActions)) { 
                                echo $numActions; 
                            }   else { echo -1; } ?>;

            if(numActions == ERROR) {
                alert("Could not retrieve $numActions from server");
            }  else {  // Proceed to Display action data    
                while(displayCount < numActions) {
                    index = displayCount;
                    actionNum = index + 1;
                    voteData = voteDataArray[index];
                    // Extract vote and vote comment from voteData array
                    try {
                        // Setup voteSelector
                        vote = voteData[VOTE];
                        voteSelector = "input[name=vote" + actionNum + "]";
                        voteSelector += "[value=" + vote + "]";
                        // Setup voteCmtSelector
                        voteCmt = voteData[VOTECMT];
                        voteCmtSelector = "#voteCmt" + actionNum;
                        // Load data
                        $(voteSelector).attr('checked','checked');
                        $(voteCmtSelector).html(voteCmt);
                    } catch(error) {
                        // This is to prevent any notifications that might
                        // occur from voteData not containing data being accessed
                        // in the try section
                    }
                    displayCount += 1;
                } // End of while(displayCount < numActions)
            } // End of else(...)
        } // End if(loadData) 
    }  // End of loadVoteData()
     function getVoteData() {
        var ERROR = -1;
        var actionCount = 0;
        var voteDataArray = [];
        var actionErrors = [];

        var numActions = <?php if(isset($numActions)) { 
                                echo $numActions; 
                            }   else { echo -1; } ?>;
        //console.log(actionInfoArray);
        if(numActions == ERROR) {
            alert("Could not retrieve $numActions from server");
        } else {
            // Begin by adding action information to voteData
            //console.log(voteDataArray);
            // get user input, one action at the time
            while(actionCount < numActions) {
                // Loop variables
                var index = actionCount;
                var action = actionCount + 1;
                // Get comment related to the current action
                var actionVoteCmt = '#voteCmt' + action; 
                var _comment = $(actionVoteCmt).val();
                // Get vote related to the current action
                actionVote = "input[name=vote" + action + "]:checked";
                var _vote = $(actionVote).val();
                // Store user action data if vote is valid
                if(_vote) {
                    var voteData = { voteCmt: _comment, vote: _vote }
                    voteDataArray.push(voteData);
                } else { // Vote missing 
                    actionErrors.push(action);
                }
                // Increment count to avoid infinite loop
                actionCount += 1;
            } // End of while loop = End of extracting data from form
        } // End of else ...
        // Notify user of any actions that require votes
        var reqVotes = "";
        $.each(actionErrors, function(index,value) {
            reqVotes += value + ", ";
        });
        if(reqVotes != "") { // Try to output the number of actions that require the users attention
            var REMOVE_CHARS = 2;
            var lastAction = reqVotes.length-REMOVE_CHARS;
            reqVotes = reqVotes.substring(0,lastAction);
            // Notify user of actions the require votes
            var reqVotesAlert = "Action: " + reqVotes + " require a vote";
            reqVotesAlert += " before submition.";
            alert(reqVotesAlert);
        } else { // if none of the actions require user attention, then return values
            return voteDataArray;
        }
    } // End of getVoteData()
    // Helper functions begin here
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
                                var msg = "merit.php : error posting to submitVote.php";
                                alert(msg);
                    }); // End of $.post(...)
                }// End of if(_pollData)
            } // End of if(userVoteData)
        } // End of if(!isReadOnly)   
    } // End of submitVote()
</script>
</html>