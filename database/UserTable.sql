/* CREATE TABLE */
CREATE TABLE User (
    user_id INT UNIQUE NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id)
    );