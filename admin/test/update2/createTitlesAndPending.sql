use voting;

DROP TABLE IF EXISTS `titles`;
CREATE TABLE `titles` (
	`t_id` int NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL,
	PRIMARY KEY (`t_id`)
);

INSERT INTO titles(title) VALUES ('Assistant Professor');
INSERT INTO titles(title) VALUES ('Associate Professor');
INSERT INTO titles(title) VALUES ('Full Professor');

DROP TABLE IF EXISTS `pending_accounts`;
CREATE TABLE `pending_accounts` (
	`email` varchar(50) NOT NULL,
  	`title` varchar(30) DEFAULT NULL,
	`password_reset` int DEFAULT 0,
  	`token` varchar(100) DEFAULT NULL,
	`pendingSince` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	PRIMARY KEY (`email`)
);
