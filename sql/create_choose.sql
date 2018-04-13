USE tinderdome;

CREATE TABLE choose (
	choose_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	chooser_id INT NOT NULL,
	chosen_id INT NOT NULL,
	choice INT,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
