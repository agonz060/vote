USE Voting;
DROP TABLE IF EXISTS Fifth_Year_Appraisal_Data;

CREATE TABLE Fifth_Year_Appraisal_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	teachingCmts varchar(500),
	researchCmts varchar(500),
	pubServiceCmts varchar(500),
	PRIMARY KEY(user_id,poll_id)
);
