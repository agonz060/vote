<?php
    session_start();
    require_once '../includes/validateSession.php';
    require_once '../includes/functions.php';

    // * NOTE: $_voteData is user data previously submitted by the user
    //                     sent from review.php
    $pollData = $_voteData = "";
    $name = $pollType = $profTitle = $dept = $effDate = "";
    // Vote data
    $comment = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // print_r($_POST);
        //echo "here";
        if(isset($_POST['pollData'])) {
            $pollData = $_POST['pollData'];
            $pollData = json_decode($pollData,true);

            $name = $pollData['name'];
            $pollType = $pollData['pollType'];
            $dept = $pollData['dept'];
            $effDate = $pollData['effDate'];
            if(isset($pollData['READ_ONLY'])) {
                $READ_ONLY = $pollData['READ_ONLY'];
            }
        }
        if(isset($_POST['voteData'])) {
            $voteData = $_POST['voteData'];
            $voteData = json_decode($voteData,true);
        }
    } // End of if($_SERVER[...])
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
<form id="myForm" style="width:70%; margin: 0 auto" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2 align="center">Faculty Confidential Advisory To Vote To The Chair</h2>
<h2 align="center">**PLEASE CAST A VOTE ON ALL DECISION**</h2>
<div>
<p class="preface">
<strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
faculty member wishes to remain anonymous and/or will not be able to attend the meeting
and would like the comments brought up at the meeting for discussion.<br>
</p>
<p class="preface">
[Use this statement for ECE & CEE advisory ballots instead of the above <strong>NOTE</strong> above..ie. Use instead:
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
I cast my vote regarding the recomendation for <?php
    $displayStr = "$name's 5<sup>th</sup>-Year Quinquennial Review in the $dept ";
    $displayStr .= "Department, effective $effDate.<br>";
    echo $displayStr;
// End PHP ?>
</p>
Satisfactory: <input type="radio" name="vote" id="satisfactory" value="1"></br>
Satisfactory with Qualifications: <input type="radio" name="vote" id="satisfactoryWithQualifications" value="4"></br>
<p id="satPreface" name="satPreface" class="preface" style="display:none">
(Please state qualifications below. A Satisfactory with qualification(s) vote can not be cast unless reasons for qualification(s)
are discussed at the department meeting)</br>
</p>
Unsatisfactory: <input type="radio" name="vote" id="unsatisfactory" value="2"></br>
Abstain: <input type="radio" name="vote" id="abstain" value="3"></br>
<hr/>
Comments:</br>
<textarea id="voteCmt" rows="8" style="width:100%"><?php
        if(isset($voteData['voteCmt'])) { echo $voteData['voteCmt']; } ?></textarea>
</div>
<hr/>
<p> Ballots must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<p>
<button type="button" id="cancel" name="cancel">Cancel</button>
<!-- <input type="submit" name="cancelVote" value="<?php if(isset($pollData['READ_ONLY'])) { echo 1; } else { echo 0; } ?>"> -->
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

$(function() { // Document
    var QUALIFICATIONS_VOTE = 4;
	$("input[type='radio'][name='vote']").change(function() {
		if(this.value == QUALIFICATIONS_VOTE) {
			$('#satPreface').show();
		}
		else {
			$('#satPreface').hide();
		}
	}); // End of voteDisplay change
    $("#submitButton").click(function() {
        //getVoteData();
        submitVote();
    }); // End submitButton action
    $('#cancel').click(function() {
        var READ_ONLY = <?php if(isset($pollData['READ_ONLY'])) { echo 1; } else { echo 0; } ?>;
        if(READ_ONLY) { // user was reviewing form
            location.href = '../user/review.php';
        } else { // user was editing form
            location.href = '../user/edit.php';
        }
    });
    $( "#effDate" ).datepicker( { dateFormat: 'yy-mm-dd' } );

});

function loadVoteData() {
    var QUALIFICATIONS_VOTE = 4;
    var loadData = <?php if(isset($pollData['READ_ONLY'])) { echo 1; }
                            else { echo 0; } ?>;
    if(loadData) {  // Load and display user data from server
        var vote = <?php if(isset($voteData['vote']))
                            { echo $voteData['vote']; }
                            else { echo 0; } ?>;
        $('input[name=vote][value='+vote+']').attr('checked','checked');
        if(vote == QUALIFICATIONS_VOTE) {
            $('#satPreface').show();
        }
    }
}

function getVoteData() {
	var IN_FAVOR_VOTE = 2;
    var _vote = $("input[name=vote]:checked").val();
    var _voteCmt = $("#voteCmt").val();
    //console.log("vote:"+_vote+" _comments:"+_comments);

    if(_vote) {
        if(_vote > 0 && _vote <= 4) {
            var voteData = { vote: _vote, voteCmt: _voteCmt };
            //console.log(voteData);
            return voteData;
        }
    } else { // Vote missing
        var voteMissing = "Please select a voting option before submitting.";
        alert(voteMissing);
        return 0;
    }
}

function submitVote() {
        var isReadOnly = <?php if(isset($pollData['READ_ONLY'])) { echo 1; }
                                else { echo 0; } ?>;
        if(!isReadOnly) {
            var userVoteData = getVoteData();
            if(userVoteData) {
                //alert(userVoteData);
                var _pollData = <?php if(isset($pollData)) { echo json_encode($pollData); } else {echo 0;} ?>;
                //alert(_pollData);
        	    console.log(_pollData);
        	    console.log(userVoteData);
                // _pollData = 0;
                if(_pollData) {
                    console.log("here");
                    $.post("../user/submitVote.php", { voteData: userVoteData, pollData: _pollData }
                                , function(data) {
                                    if(data) { // Error occured during submission
                                        alert(data);
                                    } else { // Successful submission
                                        alert("Thank you for voting!");
                                        window.location.href = "../user/edit.php";
                                    }
                                }) // End of function()
                            .fail(function(error,status) {
    				console.log(error);
    				console.log(status);
                                var msg = "quinquennial.php : error posting to submitVote.php";
                                alert(msg);
                    }); // End of $.post(...)
                }
            }
        } // End of if(!isReadOnly)
} // End of submitVote()
</script>
</html>
