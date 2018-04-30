<?php
    session_start();
    // var_dump($_SESSION);
    require_once 'includes/connDB.php';

    //var_dump($_SESSION);
    // Redirect user to correct page if already logged in
    // and cookie is still valid
    if(!(empty($_SESSION['LAST_ACTIVITY']))) {
        if(!empty($_SESSION['IDLE_TIME_LIMIT'])) {
            if(isSessionExpired()) {
                //nsignOut();
            } else {
                redirectUser();
            }
        }
    }

    // Check for idle time limit has been reached
    function isSessionExpired() {
        $lastActivity = $_SESSION['LAST_ACTIVITY'];
        $timeOut = $_SESSION['IDLE_TIME_LIMIT'];

        // Check if session has been active longer than IDLE_TIME_LIMIT
        if(time() - $lastActivity >= $timeOut) {
            return true;
        } else { false; }
    }// End of isSesssionExpired()

    function redirectUser() {
        // Session still valid, might have used the "back arrow"
        // to navigate to this page, redirect appropiately
        $ADMIN = "Administrator";
        if(!empty($_SESSION['title'])) {
            if($_SESSION['title'] == $ADMIN) {
                updateLastActivity();
                saveSessionVars();
                redirectToAdminPage();
            } else if($_SESSION['title']) {
                updateLastActivity();
                saveSessionVars();
                redirectToUserPage();
            } else { // Error log out user and remain on page
                logOut();
            }
        }
    }

    function updateLastActivity() {
        $_SESSION['LAST_ACTIVITY'] = time();
        return;
    }

    function saveSessionVars() {
        session_write_close();
        return;
    }

    function redirectToUserPage() {
        $jsRedirect = "<script type='text/javascript'>location.href='user/home.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToAdminPage() {
        $jsRedirect = "<script type='text/javascript'>location.href='admin/home.php'</script>";
        echo $jsRedirect;
        return;
    }

    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
// End of PHP ?>
<html>
<head>
	<title>BCOE Voting</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        .transparent-button {
            background:none!important;
            border:none;
            padding:0!important;
            border-bottom:1px solid #444;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#login" data-toggle="tab">BCOE Voting</a>
		</div>
	</div>
</nav>
<div style="width: 40%" class="well container center-block">
    <div>
        <ul class="nav nav-tabs">
            <a id="loginLink" data-toggle="tab" href="#login"><h4>Log In</h4></a>
        </ul>
    </div>
    <div class="tab-content center-block" >
        <div id="login" class="tab-pane fade in active">
            <div>
                <p>
                    <span id="loginErrorSpan" class="label label-danger"></span>
                </p>
            </div>
            <form class="form-signin" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input type="email" class="form-control" id="loginEmail" placeholder="Email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="loginPass" placeholder="Password">
                </div>
                <div class="form-group actionGroup">
                    <p>
                        <button id="loginButton" type="button" class="btn btn-success">Submit</button>
                        &nbsp;&nbsp;
                        <a id="resetLink" data-toggle="tab" href="#reset">Reset Password?</a>
                        &nbsp;&nbsp;
                        <a id="registerLink" data-toggle="tab" href="#register">Need to Register?</a>
                    </p>
                </div>
            </form>
        </div><!-- End of login pane -->
        <div id="reset" class="tab-pane fade in">
            <div>
                <h2>Reset password</h2>
                <p>
                    <span id="resetSuccessSpan" class="label label-success" ></span>
                </p>
            </div>
            <div class="form-group">
                <input name="email" type="email" class="form-control" id="resetEmail" placeholder="Email">
            </div>
            <div class="form-group actionGroup">
                <button id="resetButton" type="button" class="btn btn-success">Reset</button>
            </div>
        </div><!-- End of reset password pane -->
        <div id="register" class="tab-pane fade in">
            <div>
                <p> <span id="addUserSuccessSpan" class="label label-success" ></span>
                    <span id="addUserErrorSpan" class="label label-danger"></span>
                    <span id="addUserTestingSpan" class="label label-info"></span>
                </p>
            </div>
            <h2 class="form-signin-heading">Register</h2>
            <div class="form-group">
                <input class="form-control" id="addUserFirstName" placeholder="First Name" value="">
            </div>
            <div class="form-group">
                 <input class="form-control" id="addUserLastName" placeholder="Last Name" value="">
            </div>

            <!-- Select title -->
            <div class="form-group actionGroup">
                <label class="sr-only" for="addUserTitle"></label>
                <select class="form-control" id="addUserTitle">
                    <?php
                        $selectStmt = "SELECT title from titles";
                        if($result = mysqli_query($conn,$selectStmt)) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<option value=\"".$row['title']."\">".$row['title']."</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="form-group actionGroup">
                 <input class="form-control" id="addUserEmail" placeholder="Email" value="">
            </div>
            <div class="form-group actionGroup">
                 <input class="form-control" id="addUserPass1" placeholder="Enter Password">
            </div>
            <div class="form-group actionGroup">
                 <input class="form-control" type="password" id="addUserPass2" placeholder="Confirm Password">
            </div>
            <!-- Submit information if all required input is valid -->
            <button type="button" id="addUserButton" class="btn btn-success">Submit</button>
            <!-- Form ends here -->
            </form>
        </div><!-- End of registration pane -->
    </div>
</div>
</body>
<script>
    $(document).ready(function() {
        var loginError = <?php if(isset($loginError)) { echo json_encode($loginError); } else { echo 0; } ?>;
        console.log(loginError);
    });

    $("#loginLink").click(function() {
        resetSpans();
    });

    $("#resetLink").click(function() {
        resetSpans();
    });

    $("#registerLink").click(function() {
        resetSpans();
    });

    $("#resetButton").click(function() {
        var SUCCESS = 1;
        var email = $("#resetEmail").val();
        console.log(email);
        if(email && email.length > 0) {
            $("#resetSuccessSpan").text("");
            $("#resetErrorSpan").text("");

            $.post("event/reset.php", { email: email },
                        function(data) {
                            console.log(data);
                            var returnData = JSON.parse(data);
                            if(returnData.status) {
                                $("#resetSuccessSpan").text("Email sent!");
                            } else {
                                $("#resetErrorSpan").text(returnData.msg);
                            }
                        }
            ); // end of post
        }
    }); // end of $(resetButton)

    $("#loginButton").click(function() {
        // constants
        var ADMIN = "Administrator";
        var email = $("#loginEmail").val();
        var pass = $("#loginPass").val();
        console.log("email: "+email+" pass: "+pass);
        if((email && email.length > 0) && (pass && pass.length)) {
            $("#loginErrorSpan").text("");
            $.post("event/login.php", { email: email, pass: pass },
                    function(data) {
                        if(data) {
                            var returnData = JSON.parse(data);
                            if(returnData.status) {
                                if(returnData.title == ADMIN) {
                                    location.href = 'admin/home.php';
                                } else {
                                    location.href = 'user/home.php';
                                }
                            } else { // Error
                                $("#loginErrorSpan").text(data.msg);
                            }
                        }
                    } // end of function(data)
            ); // End of post
        } else {
            $("#loginErrorSpan").text("* Email or password missing.");
        }
    });

    $("#addUserButton").click(function() {
        var fName = $("#addUserFirstName").val();
        var lName = $("#addUserLastName").val();
        var title = $("#addUserTitle option:selected").text();
        var email = $("#addUserEmail").val();
        var pass1 = $("#addUserPass1").val();
        var pass2 = $("#addUserPass2").val();

        // add.php contains functions that adds user by passing along the following parameters
        $.post("admin/add.php", { firstName: fName, lastName: lName, email: email,
                                title: title, pass1: pass1, pass2: pass2 },
                function(data) {
                    if(data) {
                        $("#addUserErrorSpan").text(data);
                        $("#addUserSuccessSpan").text("");
                    } else {
                        $("#addUserErrorSpan").text("");
                        $("#addUserSuccessSpan").text("User added!");
                    }
                }
        );
    });

    function resetSpans() {
        $("#loginErrorSpan").text("");
        $("#resetSuccessSpan").text("");
        $("#addUserSuccessSpan").text("");
        $("#addUserErrorSpan").text("");
        $("#addUserTestingSpan").text("");
        $("#resetSuccessSpan").text("");
        $("#resetErrorSpan").text("");
    }
</script>
</html>
