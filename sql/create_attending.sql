USE tinderdome;

CREATE TABLE attending (
    attending_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_id_of_match INT,
	event_id INT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
alter table attending add unique `unique_user_event`(`user_id`, `event_id`);
alter table attending add unique `unique_match_user_event`(`user_id_of_match`, `event_id`);
alter table attending add unique `unique_match`(`user_id`, `user_id_of_match`);
