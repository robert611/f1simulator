CREATE DATABASE IF NOT EXISTS f1simulator;
CREATE DATABASE IF NOT EXISTS f1simulator_test;

GRANT ALL PRIVILEGES ON f1simulator.* TO 'f1simulator'@'%';
GRANT ALL PRIVILEGES ON f1simulator_test.* TO 'f1simulator'@'%';

FLUSH PRIVILEGES;
