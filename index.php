<?php session_start(); ?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
 	<tr>
		<form  method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
 		<td>
 		<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
 			<tr>
 				<td colspan="3"><strong>Member Login </strong></td>
			</tr>
			<tr>
				<td colspan="3"> <p id="loginError"></p>
				</td>
			</td>
 			<tr>
 				<td width="78">Email</td>
				<td width="6">:</td>
 				<td width="294"><input type="text" id="email"></td>
 			</tr>
 			<tr>
 				<td>Password</td>
 				<td>:</td>
 				<td><input type="password" id="pass"></td>
 			</tr>
 			<tr>
 				<td>&nbsp;</td>
 				<td>&nbsp;</td>
				<td><input type="button" id="loginButton" value="Login"></td>
				<td><a href="event/register.php">Register</a></td>
 			</tr>
 		</table>
 		</td>
 		</form>
 	</tr>
</table>

<!-- Script begins -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Output error if there is no input, otherwise post to page
    $("#loginButton").click(function() {
        var _email = $("#email").val();
        var _pass = $("#pass").val();
        var errorMsg = "<font color='red'>* Email or password empty</font>"

        if (email == '' || pass == '') {
            $("#loginError").html(errorMsg);
        } else {
            $.post("event/login.php", { email: _email, pass: _pass }, 
                function(data) { 
                    if(data != '') {
                        alert(data);
                    }
            });
        }
        var id = "<?php if(isset($_SESSION['uId'])) { echo 1; } else { echo -1; } ?>"
        if (id > 0) {
            var type = "<?php echo $_SESSION['uType']; ?>"
            if(type == "0") {
                $(document).load("user/index.php");
            } else { $(document).load("admin/index.php"); }
        }
    });

});
</script>
