CREATE DATABASE eco;
CREATE USER eco@localhost IDENTIFIED BY 'ecomarathon';

USE eco;
CREATE TABLE config (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	time TIMESTAMP,
	time_status TINYINT(1),
	time_stopped_at TIMESTAMP,
	address_for_data VARCHAR(255)
) ENGINE=InnoDB;

-- Code expects one config row to operate on
INSERT INTO config (time,address_for_data) VALUES (now(),'http://localhost/eco/values2.php');

CREATE TABLE cps (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	p1la DOUBLE NOT NULL,
	p1lo DOUBLE NOT NULL,
	p2la DOUBLE NOT NULL,
	p2lo DOUBLE NOT NULL,
	direction INT(11) NOT NULL,
	finish  TINYINT(1) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE realcps (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	cps INT(11) NOT NULL,
	distance INT(11),
	visited TINYINT(1),
	visited_at TIMESTAMP,
 	FOREIGN KEY (cps) REFERENCES cps(id)
) ENGINE=InnoDB;

CREATE TABLE laps (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	time INT(11),
	planned_time INT(11)
) ENGINE=InnoDB;

CREATE TABLE type (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255),
	unit VARCHAR(10),
	n_sensors INT(11)
) ENGINE=InnoDB;

CREATE TABLE type_sensor (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	type INT(11) NOT NULL,
	n INT(11),
	name VARCHAR(255),
	min DOUBLE,
	max DOUBLE,
	FOREIGN KEY (type) REFERENCES type(id)
) ENGINE=InnoDB;

CREATE TABLE log (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	type INT(11) NOT NULL,
	n INT(11),
	value DOUBLE,
	time TIMESTAMP,
	FOREIGN KEY (type) REFERENCES type(id)
) ENGINE=InnoDB;

CREATE TABLE gps (
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	longitude VARCHAR(8),
	latitude VARCHAR(8),
	speed DOUBLE,
	time TIMESTAMP
) ENGINE=InnoDB;

GRANT ALL ON eco.* to eco@localhost;
