<html>
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
</style>
</head>
<!-- Display webpage title -->
<h1 align="center"> User Homepage </h1>
<hr>
<form action="user/event/edit.php">
<button class="button-edit pure-button">View current ballots</button> 
</form>
<form action="user/event/review.php">
<button class="button-review pure-button">Review past ballots</button>
</body>
</html>
