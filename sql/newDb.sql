use Voting;

DROP TABLE IF EXISTS Users, Vote_Data, Voters, Polls, Professors;

Create table Polls (
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

Create table Users (
  user_id int NOT NULL AUTO_INCREMENT,
  email varchar(50) NOT NULL,
  fName varchar(30) NOT NULL,
  lName varchar(30) NOT NULL,
  password varchar(255) NOT NULL,
  title varchar(30) NOT NULL,
  PRIMARY KEY(user_id)
);

Create table Vote_Data (
  user_id int NOT NULL,
  poll_id int NOT NULL,
  firstStep int NOT NULL,
  secondStep int NOT NULL,
  vote int NOT NULL,
  voteCmt varchar(255),
  PRIMARY KEY(user_id,poll_id)
);

Create table Voters (
  user_id int NOT NULL,
  poll_id int NOT NULL,
  comment varchar(255),
  voteFlag int NOT NULL DEFAULT '0',
  Primary Key(user_id,poll_id)
);

INSERT INTO Users(email,fName,lName,password,title)
VALUES('assit','assistant','test','a1','Assistant Professor');

INSERT INTO Users(email,fName,lName,password,title)
VALUES('assoc','associate','test','a2','Associate Professor');

INSERT INTO Users(email,fName,lName,password,title)
VALUES('full','full','test','f1','Full Professor');