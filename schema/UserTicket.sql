CREATE TABLE IF NOT EXISTS `UserTicket` (
    `ticket` VARCHAR(255) NOT NULL,
	`user` VARCHAR(30) NOT NULL REFERENCES `User`(`username`),
	`url` VARCHAR(255) NOT NULL,
	`time` DATETIME NOT NULL,
	`valid` TINYINT(1) NOT NULL,
PRIMARY KEY(`ticket`, `url`));
