CREATE DATABASE IF NOT EXISTS Voting;
USE Voting;
DROP TABLE IF EXISTS Users, Vote_Data, Votes, Voters, Professors;
DROP TABLE IF EXISTS Assistant_Data, Associate_Promotion_Data, Fifth_Year_Appraisal_Data;
DROP TABLE IF EXISTS Fifth_Year_Review_Data, Reappointment_Data, Poll_Actions, Merit_Data, Other_Poll_Data; 
DROP TABLE IF EXISTS Polls;
CREATE TABLE Polls (
    poll_id int NOT NULL AUTO_INCREMENT,
    title varchar(30) NOT NULL,
    description varchar(300),
    actDate date NOT NULL,
    deactDate date NOT NULL,
    effDate date NOT NULL,
    name varchar(40) NOT NULL,
    pollType varchar(30) NOT NULL,
    otherPollTypeInput varchar(100) NOT NULL,
    dept varchar(30) NOT NULL,
    history text,
    dateModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    profTitle varchar(30) NOT NULL,
    votingOptions int NOT NULL,
    notice varchar(20) NOT NULL,
    assistantForm int NOT NULL,
    associateForm int NOT NULL,
    fullForm int NULL,
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
    pollEndDate date NOT NULL,
    comment varchar(300),
    voteFlag int NOT NULL DEFAULT '0',
    PRIMARY KEY(user_id,poll_id),
    FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);
CREATE TABLE Assistant_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);
CREATE TABLE Associate_Promotion_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	voteCmt varchar (500),
	action_num int NOT NULL,
	PRIMARY KEY(user_id,poll_id,action_num),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);

CREATE TABLE Fifth_Year_Appraisal_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	teachingCmts varchar(500),
	researchCmts varchar(500),
	pubServiceCmts varchar(500),
	PRIMARY KEY(user_id,poll_id),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);

CREATE TABLE Fifth_Year_Review_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	qualificationsCmt varchar(500),
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);

CREATE TABLE Reappointment_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	voteCmt varchar(500),
	PRIMARY KEY(poll_id,user_id),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);
CREATE TABLE Poll_Actions (
	poll_id int NOT NULL,
	action_num int NOT NULL,
	fromTitle varchar(100) NOT NULL,
	fromStep varchar(10) NOT NULL,
	toTitle varchar(100) NOT NULL,
	toStep varchar(10) NOT NULL,
	accelerated int NOT NULL,
	PRIMARY KEY(poll_id,action_num),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);
CREATE TABLE Merit_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	voteCmt varchar(500),
	action_num int NOT NULL,
	PRIMARY KEY(poll_id,user_id,action_num),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);
CREATE TABLE Other_Poll_Data (
	poll_id int NOT NULL,
	user_id int NOT NULL,
	vote int NOT NULL,
	voteCmt varchar(500),
	action_num int NOT NULL,
	PRIMARY KEY(poll_id,user_id,action_num),
	FOREIGN KEY(poll_id) REFERENCES Polls(poll_id) ON DELETE CASCADE
);