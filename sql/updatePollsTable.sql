use voting;

DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `actDate` date NOT NULL,
  `deactDate` date NOT NULL,
  `effDate` date NOT NULL,
  `name` varchar(40) NOT NULL,
  `pollType` int(10) NOT NULL,
  `otherPollTypeInput` varchar(100) DEFAULT NULL,
  `dept` int(10) NOT NULL,
  `history` text,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profTitle` int(10) NOT NULL,
  `votingOptions` int(11) DEFAULT '1',
  `notice` int(10) DEFAULT '1',
  `assistantForm` int(11) DEFAULT '0',
  `associateForm` int(11) DEFAULT '0',
  `fullForm` int(11) DEFAULT '0',
  `assistantEvaluationNum` int(10) DEFAULT '0',
  `associateEvaluationNum` int(10) DEFAULT '0',
  `fullEvaluationNum` int(10) DEFAULT '0',
  PRIMARY KEY (`poll_id`)
);

DROP TABLE IF EXISTS `assistant_data`;
DROP TABLE IF EXISTS `confidential_evals`;
CREATE TABLE `confidential_evals` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `associate_promotion_data`;
DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`poll_id`,`action_num`),
  KEY `poll_id` (`poll_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fifth_year_appraisal_data`;
DROP TABLE IF EXISTS `fifth_year_appraisals`;
CREATE TABLE `fifth_year_appraisals` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `teachingCmts` varchar(500) DEFAULT NULL,
  `researchCmts` varchar(500) DEFAULT NULL,
  `pubServiceCmts` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`poll_id`),
  KEY `poll_id` (`poll_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fifth_year_review_data`;
DROP TABLE IF EXISTS `fifth_year_reviews`;
CREATE TABLE `fifth_year_reviews` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `qualificationsCmt` varchar(500) DEFAULT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `merit_data`;
DROP TABLE IF EXISTS `merits`;
CREATE TABLE `merits` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`,`action_num`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `reappointment_data`;
DROP TABLE IF EXISTS `reappointments`;
CREATE TABLE `reappointments` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `poll_actions`;
CREATE TABLE `poll_actions` (
  `poll_id` int(11) NOT NULL,
  `action_num` int(11) NOT NULL,
  `fromTitle` varchar(100) NOT NULL,
  `fromStep` varchar(10) NOT NULL,
  `toTitle` varchar(100) NOT NULL,
  `toStep` varchar(10) NOT NULL,
  `accelerated` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`action_num`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `other_poll_data`;
DROP TABLE IF EXISTS `other_polls`;
CREATE TABLE `other_polls` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`,`action_num`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `pending_accounts`;
CREATE TABLE `pending_accounts` (
  `email` varchar(50) NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `password_reset` int(11) DEFAULT '0',
  `token` varchar(100) DEFAULT NULL,
  `pendingSince` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `titles`;
CREATE TABLE `titles` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `titles` VALUES (1,'Assistant Professor'),(2,'Associate Professor'),(3,'Full Professor');

DROP TABLE IF EXISTS `poll_types`;
CREATE TABLE `poll_types` (
	`p_id` int NOT NULL AUTO_INCREMENT,
	`poll_type` varchar(100) NOT NULL,
	PRIMARY KEY (`p_id`)
);

INSERT INTO poll_types(poll_type) VALUES ('Fifth Year Review');
INSERT INTO poll_types(poll_type) VALUES ('Fifth Year Appraisal');
INSERT INTO poll_types(poll_type) VALUES ('Merit');
INSERT INTO poll_types(poll_type) VALUES ('Promotion');
INSERT INTO poll_types(poll_type) VALUES ('Reappointment');
INSERT INTO poll_types(poll_type) VALUES ('Other');

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
	`d_id` int NOT NULL AUTO_INCREMENT,
	`department` varchar(100) NOT NULL,
	PRIMARY KEY (`d_id`)
);

INSERT INTO departments(department) VALUES ('Chemical and Environmental Engineering');
INSERT INTO departments(department) VALUES ('Computer Engineering');
INSERT INTO departments(department) VALUES ('Electrical Engineering');
INSERT INTO departments(department) VALUES ('Mechanical Engineering');
INSERT INTO departments(department) VALUES ('Bioengineering');
INSERT INTO departments(department) VALUES ('CE-CERT');

DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
	`n_id` int NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL,
	`notice` text NOT NULL,
	PRIMARY KEY (`n_id`)
);

INSERT INTO notices(type,notice) VALUES ('CEE','Comments may be submitted to the chair prior to the department meeting if the faculty member will not be able to attend the meeting and would like the comments brought up at the meeting for discussion.');
INSERT INTO notices(type,notice) VALUES ('ECE','Anonymous or absentee comments will be raised at the meeting at the Chair\'s discretion. This is in addition to the above statement i.e. Note: Comments may be submitted.... :)');
INSERT INTO notices(type,notice) VALUES ('ECE & CEE','Comments may be submitted to the chair prior to the department meeting if the faculty member will not be able to attend the meeting and would like the comments brought up at the meeting for discussion.');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `fName` varchar(30) NOT NULL,
  `lName` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`user_id`)
);

INSERT INTO `users` VALUES (1,'kzhen002@ucr.edu','Kevin','Zhen','$2y$10$Hw/sEjf7Lpankj/WxmdEEOhCqLHP/KCq7u6ZC/gmXYe9tcAxK8LQS','Administrator'),(2,'agonz060@ucr.edu','Armando','Gonzalez','$2y$10$OcDTpl32Bxc80cncN8xfZu4U9Os9Eru9.nc6HeM/A8O9YqcVCgNQe','Administrator'),(3,'assistant@gmail.com','Bevis','Buffet','$2y$10$n5zvulUyA8bnl9OnEU3UJeU7jGv8dfdIJg97h.tm/Dk8Mlz/sxq26','Assistant Professor'),(4,'associate@gmail.com','Lewis','Clark','$2y$10$ZgvD.12bNfPKK2aTOX0UcO9/HM83TqB.s9TZybLQPdwBQeL77Tqra','Associate Professor'),(7,'full@gmail.com','Luke','Cage','$2y$10$rMA6.ogkDM9L3xFtxnp2a.kOIqLDBK1QKev7gy8NmGsHaPgMdc/26','Full Professor'),(8,'admin@gmail.com','Administrator','Account','$2y$10$LDpSxzDkK9yGE.rESsDSWeuXXZNyWTsnL9hQGrrEYVEy3cypL59Je','Administrator'),(9,'agonzalez@engr.ucr.edu','Armando','Test','$2y$10$uOWjty1I9cxX8nKHpJlPveIgTpyWUZSqvVJrQ0Uvf81eLlwK1BZg.','Associate Professor'),(10,'tlindsey@engr.ucr.edu','Tiffany','Lindsey','$2y$10$QFC4BEUfaQhP4qB4Pvpyc.ObotCa7W76TxSEW3f.DwoLQwcQ92fz6','Administrator');

DROP TABLE IF EXISTS `voting_options`;
CREATE TABLE `voting_options` (
	`v_id` int NOT NULL AUTO_INCREMENT,
	`options` text NOT NULL,
	PRIMARY KEY (`v_id`)
);

INSERT INTO voting_options(options) VALUES ('In Favor, Opposed, Abstain');
INSERT INTO voting_options(options) VALUES ('Satisfactory, Unsatisfactory, Abstain');
INSERT INTO voting_options(options) VALUES ('Satisfactory, Satisfactory w/ Qualifications, Unsatisfactory');

DROP TABLE IF EXISTS `voters`;
CREATE TABLE `voters` (
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `submissionDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `voteFlag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`poll_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
);
