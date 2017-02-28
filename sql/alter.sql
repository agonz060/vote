USE Voting;
DROP TABLE IF EXISTS Fifth_Year_Appraisal_Data;
DROP TABLE IF EXISTS Fifth_Year_Review_Data;
DROP TABLE IF EXISTS Merit_Data;
DROP TABLE IF EXISTS Poll_Actions;

CREATE TABLE Poll_Actions (
    poll_id INT NOT NULL,
       

CREATE TABLE Merit_Data (
    poll_id INT NOT NULL,
    user_id INT NOT NULL,
    vote INT NOT NULL,
    voteCmt varchar(500),
    PRIMARY KEY(poll_id,user_id)

);
CREATE TABLE Fifth_Year_Appraisal_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	teachingCmts varchar(500),
	researchCmts varchar(500),
	pubServiceCmts varchar(500),
	PRIMARY KEY(user_id,poll_id)
);

CREATE TABLE Fifth_Year_Review_Data (
	poll_id INT NOT NULL,
	user_id INT NOT NULL,
	vote INT NOT NULL,
	voteCmt varchar(500),
	PRIMARY KEY(user_id,poll_id)
);
