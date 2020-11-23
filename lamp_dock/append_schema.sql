create table purchased_history (
    purchased_id int(11) auto_increment,
    user_id int(11),
    purchased_total_price int(11),
    purchased_datetime datetime cuurent_timestamp,
    primary key(purchased_id)
);

create table purchased_history_detail (
    id int(11) auto_increment,
    purchased_id int(11),
    purchased_name varchar(100) collate utf8_general_ci,
    purchased_price int(11),
    purchased_amount int(11),
    created datetime current_timestamp,
    primary key(id),
    foreign key(purchased_id)
    references purchased_history(purchased_id)
);