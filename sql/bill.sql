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




CREATE TABLE IF NOT EXISTS tb_delivery (
    delivery_id               INT(11)           NOT NULL AUTO_INCREMENT,
    delivery_item1            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item2            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,  
    delivery_item3            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item4            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item5            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,  
    delivery_item6            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item7            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item8            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,  
    delivery_item9            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item10            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item11            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item12            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,  
    delivery_item13            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item14            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,
    delivery_item15            VARCHAR(255)      CHARACTER SET utf8
                                                COLLATE utf8_general_ci NOT NULL,  
   
                                                                                                                                                                                                         
    PRIMARY KEY (delivery_id),
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

