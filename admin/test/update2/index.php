<?php
    session_start();

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
    # Login verification
    $email = $pswd = "";
    $emailErr = $pswdErr = $loginError = "";
    $DB_ERROR = "Error: could not get user data";
    $LOGIN_ERROR_MSG = "* Incorrect email / password combination";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //echo "entering POST";
        if(empty($_POST['email'])) {
            //echo 'in empty(email)'."\n";
            $emailErr = "Email is missing.";
        } else { $email = cleanInput($_POST['email']); }

        if(empty($_POST['pswd']) && !$_POST['reset']) {
            //echo 'in empty(pswd)'."\n";
            $pswdErr = "Password is missing";
        } else { $pswd = cleanInput($_POST['pswd']); }

        //echo "Email: $email Pass: $pswd\n";
    } // End of $_SERVER_REQUEST

    if(isset($email) && isset($pswd)) { // Login if credentials are valid
        //echo "verifying email and password\n";

        $getUserInfoCmd = "SELECT user_id, fName, lName, password, title ";
        $getUserInfoCmd .= "FROM Users WHERE email='$email'";
        $result = mysqli_query($conn,$getUserInfoCmd);

        if($result) { // Verify password
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $pswdHash = $row['password'];
                if(password_verify($pswd,$pswdHash)) {
                    //echo 'password verified'."\n";
                    $IDLE_TIME_LIMIT = 1200; // 1200 seconds = 20 mins
                    $ADMIN = "Administrator";
                    $id = $row['user_id'];
                    $title = $row['title'];
                    $name = $row['fName'];
                    $name .= ' '.$row['lName'];

                    $_SESSION['user_id'] = $id;
                    $_SESSION['userName'] = $name;
                    $_SESSION['title'] = $title;
                    // IDLE_TIME_LIMIT set
                    //1200 seconds = 15 mins
                    $_SESSION['IDLE_TIME_LIMIT'] = $IDLE_TIME_LIMIT;
                    $_SESSION['LAST_ACTIVITY'] = time();
                    saveSessionVars();

                     if($title == $ADMIN) { // Redirect to admin home page
                        redirectToAdminPage();
                    } else if($title) {
                        // Redirect to user profile
                        redirectToUserPage();
                    }
                } else { // Incorrect password
                        $loginError = $LOGIN_ERROR_MSG;
                }
            } else { // user with $email not found
                    $loginError = $LOGIN_ERROR_MSG;
            }
        } else { // error executing $getUserInfoCmd
                $loginError = $DB_ERROR;
        }
    } // End of login authentication

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
			<a class="navbar-brand" href="#">BCOE Voting</a>
		</div>
	</div>
</nav>
<div style="width: 70%" class="well container center-block">
    <div class="center-block">
        <p>
            <span class="label label-success center-block"></span>
        </p>
        <p>
            <span id="loginSpan" class="label label-info center-blcok"></span>
        </p>
        <?php
            $errorMsg = "";

            if(isset($loginError)) {
                $errorMsg .= $LOGIN_ERROR_MSG;
            }
            if(isset($emailErr)) {
                $errorMsg .= $emailErr;
            }
            if(isset($pswdErr)) {
                $errorMsg .= $pswdErr;
            }
            echo "<p>
                    <span id=\"errorSpan\" class=\"label label-danger center-block\">".$errorMsg."</span>
                    </p>
                ";
        ?>
    </div>
    <div class="tab-content center-block" style="width: 70%;">
        <div id="login" class="tab-pane fade in active">
            <p>
                <h2 class="form-signin-heading">Login</h2>
                <p>
                    <span id="loginErrorSpan" class="label label-danger"></span>
                </p>
            </p>
            <form class="form-signin" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <input name="pswd" type="password" class="form-control" id="pwd" placeholder="Password">
                </div>
                <div class="form-group actionGroup">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
                <div>
                    <input type="hidden" id="formAction" name="formAction" value="login">
                </div>
            </form>
        </div><!-- End of login pane -->
        <div id="reset" class="tab-pane fade in">
            <br>
            <p>
                <h2>Reset password</h2>
                <p>
                    <span id="resetSuccessSpan" class="label label-success" ></span>
                </p>
            </p>
            <form method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input name="email" type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div>
                    <input type="hidden" id="formAction" name="formAction" value="reset">
                </div>
            </form>
        </div><!-- End of reset password pane -->
        <div id="register" class="tab-pane fade in">
            <br>
            <p> <span id="addUserSuccessSpan" class="label label-success" ></span>
                <span id="addUserErrorSpan" class="label label-danger"></span>
                <span id="addUserTestingSpan" class="label label-info"></span>
            </p>
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
    <div class="center-block" style="width: 70%;">
        <ul class="nav nav-pills nav-justified">
            <li class="active"><a data-toggle="tab" href="#login">Log in</a></li>
            <li><a data-toggle="tab" href="#reset">Reset password?</a></li>
            <li ><a data-toggle="tab" href="#register">Need to register?</a></li>
        </ul>
    </div>
</div>
</body>
<script>
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
</script>
</html>
