USE tinderdome;

CREATE TABLE choose (
	choose_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	chooser_id INT NOT NULL,
	chosen_id INT NOT NULL,
	choice INT,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
alter table choose add unique `unique_chooser_chosen`(`chooser_id`, `chosen_id`);
create index index_chooser on choose (chooser_id);
create index index_chosen on choose (chosen_id);
