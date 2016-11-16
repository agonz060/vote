CREATE TABLE Professors (
	prof_id int NOT NULL AUTO_INCREMENT,
	lName varchar(25) NOT NULL,
	fName varchar(25) NOT NULL,
	title varchar(35),
	Primary key(profId)
);
CREATE TABLE Polls (
	poll_id int NOT NULL AUTO_INCREMENT,
	title varchar(40) NOT NULL,
	description varchar(300),
	actDate date NOT NULL,
	deactDate date NOT NULL,
	dateModified timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	history TEXT NOT NULL,
	Primary Key(pollId)
);
CREATE TABLE Votes (
	poll_id int NOT NULL,
	prof_id int NOT NULL,
	comment varchar(300),
	CONSTRAINT P_VoteId PRIMARY KEY(prof_id, poll_id)
);
