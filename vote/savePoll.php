<?php session_start(); ?>
<!-- Processes user input begins here -->
<?php
        # Set voting variables
        date_default_timezone_set('America/Los_Angeles');
        $day = $month = "";
        $title = $description = $actDate = $deactDate = "";
        $tmp_dateDeact = $tmp_dateAct = "";
        $errTitle = $errActDate = $errDeactDate = "";
        $validTitle =  $validActDate = $validDeactDate = false;

        # User input processing begins here
        if($_SERVER["REQUEST_METHOD"] == "POST") {
                # Check for title input; error if not provided
      		if(!empty($_POST["title"])) {
			$title = cleanInput($_POST["title"]);
		}

		if(!empty($_POST["dateActive"])) {
                	$dateAct = $_POST["dateActive"];
		}
		
		if(!empty($_POST["dateDeactive"])) {
 	               $dateDeact = $_POST["dateDeactive"];
 		}
	               
		# Process comment for selected professors
                if(!empty($_POST["voteDescription"])) {
                        $description = $_POST["voteDescription"];
                	
		}
        
	}

        function cleanInput($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
        }
?>

