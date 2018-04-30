use voting;

DROP TABLE IF EXISTS `titles`;
CREATE TABLE `titles` (
	`t_id` int NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL,
	PRIMARY KEY (`t_id`)
);

DROP TABLE IF EXISTS `pending_accounts`;
CREATE TABLE `pending_accounts` (
	`p_id` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(50) NOT NULL,
  	`fName` varchar(30) NOT NULL,
  	`lName` varchar(30) NOT NULL,
  	`title` varchar(30) NOT NULL,
  	`token` varchar(100) NOT NULL,
	`pendingSince` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	PRIMARY KEY (`p_id`)
);
