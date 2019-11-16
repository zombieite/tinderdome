USE tinderdome;

CREATE TABLE event (
	event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_short_name VARCHAR(255) NOT NULL,
    event_long_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    url varchar(255);
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
alter table event add unique `unique_event_date`(`event`, `event_date`);
