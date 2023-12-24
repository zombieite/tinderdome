USE tinderdome;

CREATE TABLE event (
	event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_class VARCHAR(255) NOT NULL,
    event_long_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    description VARCHAR(2000),
    url VARCHAR(255),
    public BOOLEAN NOT NULL,
    bounty_hunt BOOLEAN NOT NULL DEFAULT FALSE,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL
);
