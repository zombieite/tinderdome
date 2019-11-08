USE tinderdome;

CREATE TABLE attending (
    attending_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
	event_id INT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
alter table attending add unique `unique_user_event`(`user_id`, `event_id`);
