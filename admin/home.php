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
        background: rgb(102, 153, 153);
        width: 160px;
    }
</style>
</head>
<!-- Display webpage title -->
<h1 align="center">BCOE Voting Management</h1>
<hr>
<!-- Display menu options -->
<div id="menuButtons" align="center" >
    <form id="menuForm" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <button name="action" value="create" class="button-create pure-button">Create Poll</button> 
    <button name="action" value="edit" class="button-edit pure-button">Edit Poll</button>
    <button name="action" value="review" class="button-review pure-button">Review results</button>
    <button name="action" value="add" class="button-add pure-button">Add User</button>
    <button name="action" value="signOut" class="button-signOut pure-button">Sign Out</button>
    </form>
</div> 
<!-- End displaying menu buttons -->
<!-- Scripting begins -->
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
