USE Voting;

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
