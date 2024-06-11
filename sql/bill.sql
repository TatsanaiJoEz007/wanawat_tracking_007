CREATE TABLE IF NOT EXISTS tb_header {
    bill_number         VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_date           VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_customer_id    VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_customer_name  VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_total          VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_isCanceled     VARCHAR(255)    CHARACTER SET utf8
                                        COLLATE utf8_general_ci NOT NULL,
    bill_status         int(11)         NOT NULL DEFAULT 1,
    create_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (bill_number)
}ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS tb_line {
    line_id             int(11)         NOT NULL AUTO_INCREMENT,
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
    line_status         int(11)         NOT NULL DEFAULT 1,
    create_at           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (line_id),
    FOREIGN KEY (line_bill_number) REFERENCES tb_header(bill_number)
}ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Create tb_delivery table ไว้เก็บข้อมูลการส่งสินค้า
CREATE TABLE IF NOT EXISTS tb_delivery (
    delivery_id         int(11)         NOT NULL AUTO_INCREMENT,
    delivery_number     VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    delivery_truck_id   VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    delivery_date       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    delivery_status     int(11)         NOT NULL DEFAULT 1,
    PRIMARY KEY (delivery_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Create tb_delivery_items table ไว้เก็บไอเท็มที่จะส่ง
CREATE TABLE IF NOT EXISTS tb_delivery_items (
    delivery_item_id    int(11)         NOT NULL AUTO_INCREMENT,
    delivery_id         int(11)         NOT NULL,
    bill_number         VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    bill_customer_name  VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_code           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_desc           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_quantity       VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_unit           VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    item_price          VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    line_total          VARCHAR(255)    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    
    PRIMARY KEY (delivery_item_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


SELECT DISTINCT di.bill_number, di.bill_customer_name, 
                di.item_code, di.item_desc, di.item_quantity, 
                di.item_unit, di.item_price, di.line_total
FROM tb_delivery d
INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE d.delivery_status = 1;
