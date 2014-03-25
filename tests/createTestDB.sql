CREATE DATABASE replicator_tests;
CREATE USER 'replicator'@'localhost' IDENTIFIED BY 'mysql_is_evil';
GRANT ALL PRIVILEGES ON replicator_tests.* TO 'replicator'@'localhost';