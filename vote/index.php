<html>
<body>
<head>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
<style>
        .button-edit {
                color: white;
                background: rgb(28,184,65);
        	width: 80px;
	}
        .button-review {
                color: white;
                background: rgb(202,60,60);
        	width: 80px;
	}
	.button-create {
		color: white;
		background: rgb(66,184,221);
		width: 80px;
	}
</style>
</head>
<!-- Display webpage title -->
<h1 align="center"> BCOE Voting Management</h1>
<hr>
<form action="vote.php">
<button class="button-create pure-button">Create</button> 
</form>
<form action="editTable.php">
<button class="button-edit pure-button">Edit</button>
</form>
<form action="vote.php">
<button class="button-review pure-button">Review</button>
</form>
</body>
</html>
