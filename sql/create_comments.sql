USE tinderdome;

CREATE TABLE comment (
    comment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    comment_content VARCHAR(280) CHARACTER SET utf8mb4 NOT NULL,
    commenting_user_id INT NOT NULL,
    commented_on_user_id INT NOT NULL,
    approved BOOLEAN DEFAULT 0,
    number_photos INT DEFAULT 0,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
