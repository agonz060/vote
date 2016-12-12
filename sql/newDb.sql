use Voting;

drop table Users;

Create table Users (
  user_id int NOT NULL AUTO_INCREMENT,
  email varchar(50) NOT NULL,
  fName varchar(30) NOT NULL,
  lName varchar(30) NOT NULL,
  password varchar(255) NOT NULL,
  type varchar(30) NOT NULL,
  PRIMARY KEY(user_id)
);

drop table Vote_Data;

Create table Vote_Data (
  user_id int NOT NULL,
  poll_id int NOT NULL,
  firstStep int NOT NULL,
  secondStep int NOT NULL,
  vote int NOT NULL,
  voteCmt varchar(255),
  PRIMARY KEY(user_id,poll_id)
);

drop table Voters;

Create table Voters (
  user_id int NOT NULL,
  poll_id int NOT NULL,
  comment varchar(255),
  voteFlag int NOT NULL,
  Primary Key(user_id,poll_id)
);

-- Add to Polls
alter table Polls add lName varchar(30) NOT NULL;
alter table Polls add pollType varchar(30) NOT NULL;
alter table Polls add dept varchar(30) NOT NULL;
alter table Polls add effDate date NOT NULL;


insert into Users(email,fName,lName, password, type) values('smith123@gmail.com','Bob','Smith','123456','Full Professor');
insert into Users(email, fName, lName, password,type) values('thedonald@gmail.com','Donald','Trump','123456','Full Professor');
insert into Users(email,fName,lName,password,type) values('kevin@gmail.com','Kevin','Zhen','123456','Assistant Professor');

insert into Voters(user_id,poll_id,comment, voteFlag) values(1,1,'user_id1 poll_id1',0);
insert into Voters(user_id,poll_id,comment, voteFlag) values(2,1,'user_id2 poll_id1',0);
insert into Voters(user_id,poll_id,comment, voteFlag) values(3,1,'user_id3 poll_id1',0);
