CREATE TABLE IF NOT EXISTS tb_header {
    bill_id             INT(11)         NOT NULL AUTO_INCREMENT,
    bill_date           VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_number         VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_customer_id    VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_customer_name  VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_total          VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_isCanceled     VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_status         INT(11)         NOT NULL DEFAULT 1,
    create_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (bill_number)
}ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS tb_line {
    line_id             INT(11)         NOT NULL AUTO_INCREMENT,
    line_bill_number    VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_sequence       VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_code           VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_desc           VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_quantity       VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_unit           VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_price          VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_total          VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    line_weight         VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL DEFAULT 0,
    line_status         INT(11)         NOT NULL DEFAULT 1,
    create_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (line_id),
    FOREIGN KEY (line_bill_number) REFERENCES tb_header(bill_number)
}ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Create tb_delivery table ไว้เก็บข้อมูลการส่งสินค้า
CREATE TABLE IF NOT EXISTS tb_delivery (
    delivery_id                 INT(11)         NOT NULL AUTO_INCREMENT,
    delivery_number             VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    delivery_weight_total       VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 0,
    delivery_date               TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    delivery_status             INT(11)         NOT NULL DEFAULT 1,
    created_by                  INT(11)         NULL DEFAULT NULL,   

    PRIMARY KEY (delivery_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Create tb_delivery_items table ไว้เก็บไอเท็มที่จะส่ง
CREATE TABLE IF NOT EXISTS tb_delivery_items (
    delivery_item_id    INT(11)         NOT NULL AUTO_INCREMENT,
    delivery_id         INT(11)         NOT NULL,
    bill_number         VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    bill_customer_name  VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    bill_customer_id    VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_code           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_desc           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_quantity       VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_unit           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_price          VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    line_total          VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_weight         VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 0,
    transfer_type       VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    create_by           INT(11)         NULL DEFAULT NULL,
    
    PRIMARY KEY (delivery_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;




SELECT DISTINCT d.delivery_number , d.delivery_id , di.bill_number, di.bill_customer_name, 
                di.item_code, di.item_desc, di.item_quantity, 
                di.item_unit, di.item_price, di.line_total
FROM tb_delivery d
INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE d.delivery_status = 1;


SELECT DISTINCT di.bill_number, di.bill_customer_name, 
                di.item_code, di.item_desc, di.item_quantity, 
                di.item_unit, di.item_price, di.line_total
FROM tb_delivery d
INNER JOIN *.tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE d.delivery_status = 1;



SELECT
    txt.txid AS txt_id,
    txt.tx_date AS วันที่,
    txt.tx_customer AS ลูกค้า,
    txt.tx_sales AS ผู้ขาย,
    txt.tx_product AS สินค้า,
    cost.cost_price AS ราคาทุน,
    txt.tx_price AS ราคาต่อชิ้น,
    txt.tx_quantity AS จำนวนชิ้น,
    (cost.cost_price * txt.tx_quantity) AS ต้นทุน,
    (txt.tx_price * txt.tx_quantity) AS ราคาขาย,
    ((txt.tx_price * txt.tx_quantity) - (cost.cost_price * txt.tx_quantity)) AS กำไร,
    CASE
        WHEN (txt.tx_price * txt.tx_quantity) < 200 THEN '0%'
        WHEN (txt.tx_price * txt.tx_quantity) >= 200 AND (txt.tx_price * txt.tx_quantity) < 400 THEN '3%'
        WHEN (txt.tx_price * txt.tx_quantity) >= 400 AND (txt.tx_price * txt.tx_quantity) < 700 THEN '5%'
        WHEN (txt.tx_price * txt.tx_quantity) >= 700 AND (txt.tx_price * txt.tx_quantity) < 1000 THEN '7%'
        ELSE '10%'
    END AS '%com',
    FORMAT(CASE
        WHEN (txt.tx_price * txt.tx_quantity) < 200 THEN ((txt.tx_price * txt.tx_quantity) * 0) 
        WHEN (txt.tx_price * txt.tx_quantity) >= 200 AND (txt.tx_price * txt.tx_quantity) < 400 THEN ((txt.tx_price * txt.tx_quantity) * 0.03)
        WHEN (txt.tx_price * txt.tx_quantity) >= 400 AND (txt.tx_price * txt.tx_quantity) < 700 THEN ((txt.tx_price * txt.tx_quantity) * 0.05)
        WHEN (txt.tx_price * txt.tx_quantity) >= 700 AND (txt.tx_price * txt.tx_quantity) < 1000 THEN ((txt.tx_price * txt.tx_quantity) * 0.07)
        ELSE ((txt.tx_price * txt.tx_quantity) * 0.10)
    END, 2) AS ค่าคอมมิชชั่น ,
    sale.sale_nickname AS ชื่อเล่นเซลล์,
    country.customer_country AS ประเทศ
FROM tb_txtdata AS txt
LEFT JOIN tb_cost AS cost ON txt.tx_product = cost.cost_product
LEFT JOIN tb_salesbio AS sale ON txt.tx_sales = sale.sale_id
LEFT JOIN tb_country AS country ON txt.tx_customer = country.customer_id

WHERE (txt.tx_price * txt.tx_quantity) > 700 AND country.customer_country = 'ลาว'  
ORDER BY `ชื่อเล่นเซลล์` ASC


SELECT 
	SUM(txt.tx_price * txt.tx_quantity) AS ยอดรวม,
    country.customer_country AS ประเทศ
    
FROM 
    tb_txtdata AS txt
LEFT JOIN 
    tb_country AS country ON txt.tx_customer = country.customer_id
GROUP BY 
    country.customer_country  
ORDER BY `ยอดรวม` DESC;

SELECT 
	SUM((txt.tx_price - cost.cost_price)*txt.tx_quantity) AS profit,
    country.customer_country AS ประเทศ
    
FROM 
    tb_txtdata AS txt
LEFT JOIN 
    tb_country AS country ON txt.tx_customer = country.customer_id
LEFT JOIN
	tb_cost AS cost ON txt.tx_product = cost.cost_product
GROUP BY 
    country.customer_country  
ORDER BY `profit` DESC;