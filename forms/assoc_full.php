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
<?php
    // Form data 
    $lastName = $pollType = $profTitle = $dept = $effDate = "";
    $dataErrorMsg = "Error loading form data";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["lastName"])) {
            $lastName = $_POST["lastName"];
        } else { $lastName = $dataErrorMsg; }

        if(isset($_POST["pollType"])) {
            $pollType = $_POST["pollType"];
        } else { $pollType = $dataErrorMsg; }

        if(isset($_POST["profTitle"])) {
            $pollType = $_POST["profTitle"];
        } else { $pollType = $dataErrorMsg; }

        if(isset($_POST["dept"])) {
            $pollType = $_POST["dept"];
        } else { $pollType = $dataErrorMsg; }
    
        if(isset($_POST["effDate"])) {
            $pollType = $_POST["effDate"];
        } else { $pollType = $dataErrorMsg; }
    
        if(isset($_POST["fromStep"])) {
            $fromStep = $_POST["fromStop"];
        }
    
        if(isset($_POST["toStep"])) {
            $toStep = $_POST["toStep"];
        }
        
        if(isset($_POST["vote"])) {
            $vote = $_POST["vote"];
        }

        if(isset($_POST["comment"])) {
            $comment = $_POST["comment"];
        }
    }
?>


<form style="width:70%; margin: 0 auto" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<h2 align="center">Faculty Confidential Advisory To Vote To The Chair</h2>
<div>
<p class="preface">
<strong>NOTE:</strong> Comment may be submitted to the chair prior to the department meeting if the
faculty member wishes to remain anonymous and/or will not be able to attend the meeting 
and would like the comments brought up at the meeting for discussion.<br>
</p>
<p class="preface">
[Use this statement for CEE advisory ballots instead of the above <strong>NOTE..:</strong>
Comment may be submitted to the chair prior to the department meeting if the faculty member will
not be able to attend the meeting and would like the comments brought up at the meeting for discussion.]<br>
</p>
<p class="preface">
(Use this statement EE advisory ballots: Anonymous or absentee comments will be raised at the
meeting at the Chair's discretion. .....This is in addition to the above
<strong>NOTE:</strong> Comments may be submitted....)<br>
</p>
<p class="preface">
Comments not discussed at the meeting will not be reflected in the department letter.
</p>
</div>
<hr>
<div>
<p> 
I cast my vote regarding the recomendation for <?php echo "$lastName's $pollType from $profTitle,"; ?>
Step
<select class="selector" id="fromLevel" name="fromLevel">
	<option>I</option>
	<option>II</option>
	<option>III</option>
	<option>IV</option>
</select> (OS) to 
<select class="selector" id="toLevel" name="toLevel">
	<option>I</option>
	<option>II</option>
	<option>III</option>
	<option>IV</option>
	<option>V</option> 
</select> in the Department of <?php echo "$dept"; ?>
, effective <?php echo "$effDate"; ?>.</br>
</p>
In Favor: <input type="radio" name="vote" id="vote" value="0">&nbsp;&nbsp;&nbsp;  
Opposed: <input type="radio" name="vote" id="vote" value="1">&nbsp;&nbsp;&nbsp;
Abstain: <input type="radio" name="vote" id="vote" value="2"></br>
<hr>
Comments:<br>
<textarea id="comment" name= "comment" rows="8" style="width:100%"><?php if(isset($comment)) { echo "$comment"; } ?></textarea>
</div>
<hr>
<p> Ballots must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the 
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<p>
<input type="button" value="Cancel">
<input type="button" value="Save">
<input type="button" value="Submit">
</p>
</form>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
	$( "#effDate" ).datepicker( { dateFormat: 'yy-mm-dd' } );
});
</script>
</body>
</html>
