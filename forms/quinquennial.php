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
<form id="myForm" style="width:70%; margin: 0 auto" method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
I cast my vote regarding the recomendation for
<select class="selector">
	<option>Last Name</option>
	<option>Trump</option>
</select>'s 5<sup>th</sup>-Year Quinquennial Review in the 
<input size="15" type="text" name="Dept" id="Dept" value=""/> Department
, effective <input size="10" type="text" name="effDate" id="effDate"/>.</br>
</p>
Satisfactory: <input type="radio" name="vote" id="satisfactory" value="0"></br>  
<p id="satPreface" name="satPreface" class="preface" style="display:none">
(Please state qualifications below. A Satisfactory with qualification(s) vote can not be cast unless reasons for qualification(s)
are discussed at the department meeting)</br>
</p>
<textarea id="qualifications" name="qualifications" row="8" style="width:100%; display:none" value=""></textarea>
Unsatisfactory: <input type="radio" name="vote" id="unsatisfactory" value="1"></br>
Abstain: <input type="radio" name="vote" id="abstain" value="2"></br>
<hr/>
Comments:</br>
<textarea id="comments" name="comments" rows="8" style="width:100%"></textarea>
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

$(function() {
	$("input[type='radio'][name='vote']").change(function() {
		if(this.value == 0) {
			$('#qualifications').show();
			$('#qualifications').prop('required',true);
			$('#satPreface').show();
		}
		else {
			$('#qualifications').hide();
			$('#qualifications').prop('required',false);
			$('#satPreface').hide();
		}
	});
});
</script>
</body>
</html>
