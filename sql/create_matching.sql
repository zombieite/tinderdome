USE tinderdome;

CREATE TABLE matching (
	matching_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event VARCHAR(255) NOT NULL,
	year INT NOT NULL,
	user_1 INT NOT NULL,
	user_2 INT NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
alter table matching add unique `unique_user_1_user_2`(`user_1`, `user_2`);
