CREATE TABLE __TABLE__ (
    id INT AUTO_INCREMENT,
    hash CHAR(40),
    creation_date DATETIME,
    confirmation_date DATETIME,
    booking INT,
    PRIMARY KEY (id),
    INDEX (creation_date),
    INDEX (confirmation_date),
    UNIQUE (hash)
)
