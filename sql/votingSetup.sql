USE Voting;

DROP TABLE IF EXISTS Users, Vote_Data, Votes, Voters, Polls, Professors;

CREATE TABLE Polls (
    poll_id int NOT NULL AUTO_INCREMENT,
    title varchar(30) NOT NULL,
    description varchar(300),
    actDate date NOT NULL,
    deactDate date NOT NULL,
    effDate date NOT NULL,
    name varchar(40) NOT NULL,
    pollType varchar(30) NOT NULL,
    dept varchar(30) NOT NULL,
    history text,
    dateModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(poll_id)
    
);

CREATE TABLE Users (
    user_id int NOT NULL AUTO_INCREMENT,
    email varchar(50) NOT NULL,
    fName varchar(30) NOT NULL,
    lName varchar(30) NOT NULL,
    password varchar(255) NOT NULL,
    title varchar(30) NOT NULL,
    PRIMARY KEY(user_id)
);

CREATE TABLE Voters (
    user_id int NOT NULL,
    poll_id int NOT NULL,
    comment varchar(300),
    voteFlag int NOT NULL DEFAULT '0',
    PRIMARY KEY(user_id,poll_id)
);

CREATE TABLE Assistant_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id)

);

CREATE TABLE Associate_Promotion_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	fromLevel INT NOT NULL,
	toLevel INT NOT NULL,
	vote INT NOT NULL,
	voteCmt varchar (500),
	PRIMARY KEY(user_id,poll_id)
);

CREATE TABLE Fifth_Year_Appraisal_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	teachingCmt varchar(500),
	researchCmt varchar(500),
	pubServiceCmt varchar(500),
	PRIMARY KEY(user_id,poll_id)
);

CREATE TABLE Fifth_Year_Review_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	qualificationsCmt varchar(500),
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id)
);

CREATE TABLE Reappointment_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id)
);
