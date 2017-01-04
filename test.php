<?php 
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
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
<input type="submit" name="incr" value="incr-button">
</form>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>	
<script>

function timeTest() {
	location.reload(true);

	alert("Act time: "+actTime+" Current time: "+currTime+"\nTime since act: "+timePassed);
	
};		

$(document).ready(function() {


		setTimeout(timeTest,4000);
	//var t = 1;
	//if(t) { alert('T = 1'); } else { alert('t = 0'); }
});
</script>
