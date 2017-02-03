<?php // Session verification 
    session_start();
    require_once "event/sessionHandling.php";
    require_once "event/redirections.php";                                                                                                                                                                  
    if(!isAdmin()) {
        signOut();
    } else if(idleLimitReached()) {
        signOut();
    }
 // End Session verification?>
<?php
    require_once 'event/connDB.php'; 
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
/* $_SERVER(POST) ends here */
?>
<body>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
    .button-edit {
        text-align: center;
        color: white;
        background: rgb(28,184,65);
    	width: 160px;
	}
    .button-review {
        text-align: center;
        color: white;
        background: rgb(202,60,60);
    	width: 160px;
	}
	.button-create {
        text-align: center;
		color: white;
		background: rgb(66,184,221);
		width: 160px;
	}
    .button-add {
        text-align: center;
        color: white;
        background: rgb(255,140,0);
        width: 160px;
    }
    .button-signOut {
        text-align: center;
        color: white;
        background: red;
        width: 160px;
    }
    .navbar {
	margin-bottom: 0px;
    }
</style>
</head>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="active"><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit/editTable.php">Edit Poll</a></l>
			<li><a href="edit/reviewTable.php">Review Poll</a></li>
			<li><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<div class="jumbotron">
	<h1>Welcome <?php echo htmlspecialchars($_SESSION["userName"]); ?>!</h1>
	<p>You have # polls to vote on.</p>
</div>
<!-- Display menu options -->
<div id="menuButtons" align="center" >
    <form id="menuForm" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <button name="action" value="signOut" class="btn btn-danger">Sign Out</button>
    </form>
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
