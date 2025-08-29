// MySQL-Tabelle: SQL-Schema

CREATE TABLE investments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cert_number VARCHAR(50) NOT NULL,
  investor_name VARCHAR(255) NOT NULL,
  amount DECIMAL(20,2) NOT NULL,
  date DATETIME NOT NULL
);