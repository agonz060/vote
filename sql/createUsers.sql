use voting;

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
