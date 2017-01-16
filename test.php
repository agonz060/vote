<?php 
	session_start();
	$_SESSION['title'] = "Success sucka!!";
	// Test $SESSION variables on ajax post
	// will vars hold their value?
	require_once 'event/connDB.php';

	function getAssistantData() {
        global $conn;
        $data = array();

        $SELECT_CMD = "SELECT * FROM Assistant_Data WHERE user_id=1 AND ";
        $SELECT_CMD .= "poll_id=3";

        $result = mysqli_query($conn,$SELECT_CMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $data = $row;
            }
            //echo 'Vote data: '; print_r($data);
            return json_encode($data);
            //return $data;     
        } else { // Error occured while executing command
            return -1;
        }
    }

	$x = getAssistantData();

	echo "encoded: $x";

	$d = json_decode($x,true);
	echo "decoded: "; print_r($d);

	$c = $d['voteCmt'];
	echo "cmt: $c";

	/*$x = array();
	if(empty($x)) {
		echo "x is empty<br>";
	}

	date_default_timezone_set('America/Los_Angeles');
	$endDate = "2017-01-09";
	$endDate_time = strtotime($endDate);
	$current_time = strtotime("now");
	$today_time = strtotime("today");

	echo "currentTime: $current_time<br>todayTime: $today_time<br>";

	if($current_time < $endDate_time) {
		echo "current time < end time<br>";
		echo "2017-01-03 < 2017-01-09<br>";
		echo "$current_time".' < '."$endDate_time<br>";
	}

	$sixDays = strtotime("+6 day");
	echo "six days: $sixDays<br>";
	$sixDays = $current_time + $sixDays;

	echo "endDate: $endDate_time <br>";
	echo 'current + 6 day: ';
	
	"<br>";
	/*session_start();
	print_r($_SESSION);

	$_SESSION['varNames'] = [];


	<form method="post">
		<input type="submit" name="submit" value="Submit">
		<input type="submit" name="cancel" value="Cancel">
	</form>
	//echo pathinfo();
	$lifeTime = 30;

	session_start();
	if(!isset($_SESSION['test'])) {
		$_SESSION['test'] = time();
	} else {
			echo "test: ".$_SESSION['test']."\n";
			echo "time: ".time()."\n";
			$elapsed = time() - $_SESSION['test'];
			echo "elapsed: ".$elapsed."\n";
			if($elapsed > 2) {
				session_unset();
				session_destroy();
				session_regenerate_id(true);
				session_name("TimeoutSession");
				session_start();
			}
	}
	*/
?>

<?php 
	/*function testing() {
		echo "test complete";
	}
	$x = 0;

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST['incr'])) {
			echo "x: $x";
			$x += 1;
		}
	}

	$a = [];
	$a['armando gonzalez'] = "comment 1";
	$a['ethan gonzalez'] = "ethans the man";

	print_r($a);

	$n = 'armando gonzalez';
	echo $a[$n];
	*/
?>
<p id="alertP"></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
<!-- <input type="submit" name="incr" value="incr-button"> -->
<input type="submit" name="submit" value="submit">
<input type="button" id="submitButton" name="submit" value="submit" action="alert()">
</form>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>	
<script>

function timeTest() {
	location.reload(true);
	alert("Act time: "+actTime+" Current time: "+currTime+"\nTime since act: "+timePassed);
};		

$(document).ready(function() {
	$("#submitButton").click(function() {
		submitTest();
	});

		//setTimeout(timeTest,4000);
	//var t = 1;
	//if(t) { alert('T = 1'); } else { alert('t = 0'); }
});

function submitTest() {
	var _voteData = <?php if(!empty($x)) { echo json_encode($x); } else { echo 0; } ?>;
	if(_voteData) {
		$.post("submitTest.php", { voteData: _voteData }
			, function(data) {
				alert(data);
				$("#alertP").val(data);
		});
	}
};
</script>
