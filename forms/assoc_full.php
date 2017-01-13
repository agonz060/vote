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
    var_dump($_POST);
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // Following Post variables are polling data
        if(!empty($_POST["profName"])) {
            $lastName = $_POST["profName"];
        } else { $lastName = $dataErrorMsg; }

        if(!empty($_POST["pollType"])) {
            $pollType = $_POST["pollType"];
        } else { $pollType = $dataErrorMsg; }
        //not posted
        if(!empty($_POST["dept"])) {
            $dept = $_POST["dept"];
        } else { $dept = $dataErrorMsg; }

        if(!empty($_POST["effDate"])) {
            $effDate = $_POST["effDate"];
        } else { $effDate = $dataErrorMsg; }
    
        if(!empty($_POST["deactDate"])) {
            $deactDate = $_POST["deactDate"];
        } else { $deactDate = $dataErrorMsg; }
        // The following four posts are user data
        if(!empty($_POST["fromStep"])) {
            $fromStep = $_POST["fromStep"];
        }
    
        if(!empty($_POST["toStep"])) {
            $toStep = $_POST["toStep"];
        }
        
        if(!empty($_POST["vote"])) {
            $vote = $_POST["vote"];
        }

        if(!empty($_POST["comment"])) {
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

<?php
    if($dept === "Computer Engineering") {
	echo '<p class="preface">';
	echo htmlspecialchars("[Use this statement for CEE advisory ballots instead of the above ");
	echo '<strong>NOTE..:</strong>';
	echo htmlspecialchars("Comment may be submitted to the chair prior to the department meeting if the faculty member will
	not be able to attend the meeting and would like the comments brought up at the meeting for discussion.]");
	echo '<br></p>';
    }
    if($dept === "Electrical Engineering") {
	echo '<p class="preface">';
	echo htmlspecialchars("(Use this statement for EE advisory ballots: Anonymous or absentee comments will be raised at the
	meeting at the Chair's discretion. .....This is in addition to the above");
	echo '<strong>NOTE:</strong>';
        echo htmlspecialchars("Comments may be submitted....)");
	echo '<br></p>';
    }
?>

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
<textarea id="comment" name= "comment" rows="8" style="width:100%"><?php if(!empty($comment)) { echo "$comment"; } ?></textarea>
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

</body>
</html>
