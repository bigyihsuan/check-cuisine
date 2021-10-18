/* CREATE TABLE */
CREATE TABLE IF NOT EXISTS `Users` (
	`id` INT NOT NULL AUTO_INCREMENT
	,`username` VARCHAR(100) NOT NULL
	,`password` VARCHAR(60) NOT NULL
	,PRIMARY KEY (`id`)
	,UNIQUE (`username`)
	)
