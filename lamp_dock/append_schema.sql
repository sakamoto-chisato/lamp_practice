CREATE TABLE purchased_history (
    purchased_id INT(11) AUTO_INCREMENT,
    user_id INT(11),
    purchased_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(purchased_id)
);

CREATE TABLE purchased_history_detail (
    id INT(11) AUTO_INCREMENT,
    purchased_id INT(11),
    purchased_name VARCHAR(100) COLLATE utf8_general_ci,
    purchased_price INT(11),
    purchased_amount INT(11),
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id),
    FOREIGN KEY(purchased_id)
    REFERENCES purchased_history(purchased_id)
);