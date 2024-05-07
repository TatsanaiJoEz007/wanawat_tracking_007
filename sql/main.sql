CREATE DATABASE IF NOT EXISTS wanawat_tracking CHARACTER SET utf8 COLLATE utf8_general_ci;

USE wanawat_tracking;

CREATE TABLE IF NOT EXISTS tb_freq (
    freq_id         INT             AUTO_INCREMENT  NOT NULL,
    freq_header     VARCHAR(255)    CHARACTER SET utf8
							        COLLATE utf8_general_ci NOT NULL,
    freq_content    VARCHAR(255)    CHARACTER SET utf8
							        COLLATE utf8_general_ci NOT NULL,
    freq_create_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    freq_status     TINYINT         DEFAULT 1,
    
    PRIMARY KEY (freq_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tb_banner (
    banner_id         INT             AUTO_INCREMENT  NOT NULL,
    banner_img     VARCHAR(255)    CHARACTER SET utf8
							        COLLATE utf8_general_ci NOT NULL,
    banner_create_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    banner_status     TINYINT         DEFAULT 1,

    PRIMARY KEY (banner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tb_question (
    question_id                 INT             AUTO_INCREMENT  NOT NULL,
    question_sender_name        VARCHAR(255)    NOT NULL,
    question_sender_email       VARCHAR(255)    NOT NULL,
    question_content            VARCHAR(255)    NOT NULL,
    question_create_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    question_status             TINYINT         DEFAULT 1,
    
    PRIMARY KEY (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tb_user (
    user_id             INT AUTO_INCREMENT,
    user_firstname      VARCHAR(255) NOT NULL,
    user_lastname       VARCHAR(255) NOT NULL,
    user_email          VARCHAR(255) NOT NULL,
    user_pass           VARCHAR(255) NOT NULL,
    user_img            VARCHAR(255) NOT NULL,
    user_type           INT(50) NOT NULL,
    user_address        VARCHAR(255) NOT NULL,
    district_id         INT NOT NULL,
    user_tel            VARCHAR(255) NOT NULL,
    user_create_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_status         TINYINT DEFAULT 1,

    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS tb_category (
    category_id             INT                 AUTO_INCREMENT,
    category_name           VARCHAR(255)        NOT NULL,
    category_create_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    category_status         TINYINT             DEFAULT 1,

    PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tb_product (
    product_id              INT                 AUTO_INCREMENT,
    product_name            VARCHAR(255)        NOT NULL,
    product_height          DECIMAL(10,2)       NOT NULL,
    product_width           DECIMAL(10,2)       NOT NULL,
    product_weight          DECIMAL(10,2)       NOT NULL,
    category_id             INT                 NOT NULL,
    product_create_at       TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    product_status          TINYINT             DEFAULT 1,
    
    PRIMARY KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
