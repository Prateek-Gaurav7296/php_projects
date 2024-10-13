GRANT ALL PRIVILEGES ON *.* TO 'app_user' @'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS url_shortener;

USE url_shortener;

CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_url VARCHAR(255) NOT NULL,
    short_code VARCHAR(10) UNIQUE NOT NULL,
    created_date DATE NOT NULL,
    access_frequency INT
);