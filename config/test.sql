CREATE DATABASE IF NOT EXISTS TUNISPACE
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE TUNISPACE;
SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE pizza_lovers(
    id INT AUTO_INCREMENT PRIMARY KEY,
    pizza_name VARCHAR(50) NOT NULL UNIQUE,
    pizza_pass VARCHAR(255) NOT NULL ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fav_pizza VARCHAR(10),
    bio TEXT

)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- make some data
INSERT INTO pizza_lovers(id,pizza_name,pizza_pass,fav_pizza,bio) VALUES
(67,'lain',123,'pineapple','I like pineapple pizza :3'),
(2,'nada',123,'Margherita','I like my pizza thin crust with cheese'),
(3,'C-STXRM',123,'Pepperoni','I had to pick between Quad chess and pepperoni :('),
(4,'ahmed',123,'pizza','pizza mozzarella rella rella rella rella rella ');

ALTER TABLE pizza_lovers
DROP INDEX pizza_name;

