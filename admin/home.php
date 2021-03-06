<?php // Session verification
    session_start();
    require_once 'includes/sessionHandling.php';
    require_once 'includes/redirections.php';
    require_once 'includes/connDB.php';

    // if(!isAdmin()) {
    //     signOut();
    // }

    // End Session verification
     /* $_SERVER(POST) starts here */
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($_POST['action'])) {
            // Menu options
            $ADD = "add";
            $EDIT = "edit";
            $CREATE = "create";
            $REVIEW = "review";
            $SIGNOUT = "signOut";

            if(!idleLimitReached()) {
                $action = $_POST['action'];
                if($action == $ADD) {
                    saveSessionVars();
                    redirectToAddPage();
                } else if($action == $CREATE) {
                    saveSessionVars();
                    redirectToVotePage();
                } else if($action == $EDIT) {
                    saveSessionVars();
                    redirectToEditPage();
                } else if($action == $REVIEW) {
                    saveSessionVars();
                    redirectToReviewPage();
                } else if($action == $SIGNOUT) {
                    signOut();
                    saveSessionVars();
                    redirectToLogIn();
                }
            } else { // End of isValidSession()
                signOut();
            } // End of else
        }
    } // End of $_SERVER['REQUEST_METHOD']

    function getOutstandingVotes($conn, $user_id) {
	    $outstandingVotes = 0;
	    $query = "SELECT count(user_id) AS COUNT FROM Voters WHERE user_id=?";
	    $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
	    mysqli_stmt_bind_param($stmt, "s", $user_id) or die(mysqli_error($conn));
	    mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
	    mysqli_stmt_bind_result($stmt, $outstandingVotes) or die(mysqli_error($conn));
	    mysqli_stmt_fetch($stmt);
	    return $outstandingVotes;
    }
    $outstandingVotes = getOutstandingVotes($conn, $_SESSION['user_id']);
?>
<head>
<title>Home</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
    .navbar {
	margin-bottom: 0px;
    }
    div.jumbotron {
	background-image: url('../images/home.jpg');
	background-size: cover;
	color: white;
	background-repeat: no-repeat;
	text-shadow: black 1px 1px 1px;
	min-height: 100%;
	text-align: center;
	margin-bottom: 0;
    }
    .btn-transparent {
    	background-color: transparent;
	border-color: white;
    }
</style>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="home.php">BCOE Voting</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="home.php">Home</a></li>
            <li><a href="vote.php">Create Poll</a></li>
            <li><a href="edit.php">Edit Poll</a></li>
            <li><a href="review.php">Review Poll</a></li>
            <li><a href="manage.php">Manage Users</a></li>
        </ul>
    </div>
</nav>
<div class="jumbotron">
	<h1>Welcome <?php echo htmlspecialchars($_SESSION["userName"]); ?>!</h1>
	<!-- Display menu options -->
	<div id="menuButtons" align="center" >
    		<form id="menuForm" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    			<button name="action" value="signOut" class="btn btn-transparent">Sign Out</button>
    		</form>
	</div>

	<!--
	<p>You have <?php echo $outstandingVotes; ?> polls to vote on.</p>
	-->
</div>
<!-- End displaying menu buttons -->
<!-- Scripting begins -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Set interval to reload page for user authentication purposes
        setInterval(reloadPage,1200000); //1200000 ms = 1200 s = 20 mins
    });

    function reloadPage() {
        location.reload();
    };
</script> <!-- Scripting ends here -->
</body> <!-- Document body ends here -->
</html>