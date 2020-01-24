CREATE DATABASE IF NOT EXISTS `TrabalhoSGC` CHARACTER SET utf8 COLLATE utf8_unicode_ci;

# Create user accessing from localhost
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'segredo';

# Create user accessing from remote hosts
CREATE USER 'admin'@'%' IDENTIFIED BY 'segredo';

# Grant usages
GRANT USAGE ON * . * TO 'admin'@'localhost' IDENTIFIED BY 'segredo' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
GRANT USAGE ON * . * TO 'admin'@'%' IDENTIFIED BY 'segredo' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

# Grant privileges
GRANT ALL PRIVILEGES ON `TrabalhoSGC` . * TO 'admin'@'localhost';
GRANT ALL PRIVILEGES ON `TrabalhoSGC` . * TO 'admin'@'%';
