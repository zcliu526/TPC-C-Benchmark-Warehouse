LOAD DATA LOCAL INFILE 'warehouse.csv' INTO TABLE warehouse COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'district.csv' INTO TABLE district COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'customer.csv' INTO TABLE customer COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'history.csv' INTO TABLE history COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'order.csv' INTO TABLE `order` COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'new_order.csv' INTO TABLE new_order COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'order_line.csv' INTO TABLE order_line COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'item.csv' INTO TABLE item COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
LOAD DATA LOCAL INFILE 'stock.csv' INTO TABLE stock COLUMNS TERMINATED BY ',' LINES TERMINATED BY '\n';
