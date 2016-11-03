<?php session_start(); ?>
<html>
<head>
<script type="text/javascript"
src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function() {
        $("#saveCmt").click(function(e) {
                var txtCmt  = $("#cmtText").val();
                $.post("saveCmt.php", {comment : txtCmt},
                function(response,status) {
                        alert("response: " + response+"\n\nStatus: " + status);
                 });
        });
});
</script>
</head>

<body>
<form name="profCmt" id="profCmt" >
<textarea name="cmtText" id="cmtText" rows="3" cols="30"></textarea>
</form>
<input type="button" id="saveCmt" name="saveCmt" value="Save"  >

</body>
</html>
