CREATE TABLE Professors (
	profId int NOT NULL AUTO_INCREMENT,
	lName varchar(25) NOT NULL,
	fName varchar(25) NOT NULL,
	title varchar(35),
	Primary key(profId)
);
CREATE TABLE Polls (
	pollId int NOT NULL AUTO_INCREMENT,
	title varchar(40) NOT NULL,
	description varchar(300),
	actDate date NOT NULL,
	deactDate date NOT NULL,
	dateModified timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	history TEXT NOT NULL,
	Primary Key(pollId)
);
CREATE TABLE Votes (
	pollId int NOT NULL,
	profId int NOT NULL,
	comment varchar(300),
	CONSTRAINT pVoteId PRIMARY KEY(profId, pollId)
);
