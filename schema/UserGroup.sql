CREATE TABLE IF NOT EXISTS `UserGroup` (
	`user` VARCHAR(30) NOT NULL REFERENCES `User`(`username`),
	`groupName` VARCHAR(30) NOT NULL REFERENCES `Group`(`name`),
PRIMARY KEY(`user`, `group`));
