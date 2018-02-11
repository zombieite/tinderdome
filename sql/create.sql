USE tinderdome;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	remember_token VARCHAR(100),
	email VARCHAR(255),
	number_people INT,
	gender VARCHAR(1),
	height INT,
	birth_year INT,
	description VARCHAR(2000),
	how_to_find_me VARCHAR(200),
	number_photos INT,
	random_ok BOOLEAN,
	hoping_to_find_acquaintance BOOLEAN,
	hoping_to_find_friend BOOLEAN,
	hoping_to_find_love BOOLEAN,
	hoping_to_find_lost BOOLEAN,
	hoping_to_find_enemy BOOLEAN,
	attending_winter_games BOOLEAN,
	attending_ball BOOLEAN,
	attending_detonation BOOLEAN,
	attending_wasteland BOOLEAN,
	ip VARCHAR(50),
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
