<html>
<body>

<!-- Begin form -->
<form action="/index.php" method="post">

<!-- Enter name and email for logging purposes and to insert into database -->
Name: <input type="text" name="name"><br /><br />
E-mail: <input type="text" name="email"><br /><br />

<!-- Select title -->
Title: 
<input type="radio" name="title" value="assistant">Assistant Professor
<input type="radio" name="title" value="associate">Associate Professor
<input type="radio" name="title" value="full">Full Professor
<br /><br />


<!-- Submit information -->
<input name="submit" type="submit" value="Submit" />

<!-- Form ends here -->
</form>

</body>
</html>

