<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript">
	$(function() {
		$("#saveCmt").click(function(e) {
			e.preventDefault();
			var comment = $("#comment").val();
			$.ajax({
				type: "POST",
				url: "saveCmt.php",
				data: {comment:comment},
				success: function() {
				 	alert("comment saved");	
				},
				error:function(e){alert("failed to save");}
			});
		});
});
</script>
</head>

<body>

<form name="cmtBox" id="cmtBox" action="saveCmt.php" method="post">
<textarea name="comment" rows="3" cols="30"></textarea>
<input type="button" id="saveCmt" name="saveCmt" value="Save"  >

<p name="result" id="result"></p>
</form>

</body>
</html>

