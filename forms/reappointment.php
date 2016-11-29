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
<select class="selector">
	<option>Last Name</option>
	<option>Trump</option>
</select>'s 
Reappointment File in the Department of <input size="15" type="text" name="Dept" id="Dept" value=""/>
, effective <input size="10" type="text" name="effDate" id="effDate"/>. 
<i>Please note that a vote against reappointment may result in the Chancellor's final decision not to
reappoint with a terminal notice being given to the candidate.</i></br>
</p>
In Favor: <input type="radio" name="vote" id="vote" value="0">&nbsp;&nbsp;&nbsp;  
Opposed: <input type="radio" name="vote" id="vote" value="1">&nbsp;&nbsp;&nbsp;
Abstain: <input type="radio" name="vote" id="vote" value="2"></br>
<hr/>
Comments:</br>
<textarea rows="8" style="width:100%"></textarea>
</div>
<hr/>
<p> Ballots must be received by the BCOE Central Personnel Services Unit(CSPU) Office or the 
department FAO within <strong><u>TWO DAYS</u></strong> following the department meeting.
<span style="color: #FF0000; font-weight:bold">All absentee ballots must be recieved <u>prior</u> to the department meeting.</span>
</p>
<p>
<input type="button" value="Submit">
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
