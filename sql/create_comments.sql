USE tinderdome;

CREATE TABLE comments (
    comment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    commenter_user_id INT NOT NULL,
    comment_on_user_id INT NOT NULL,
    approved BOOLEAN,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
