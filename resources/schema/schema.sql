CREATE TABLE `categories` (
  `id` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`) VALUES
('22e5f98c-48cb-4e5e-b14f-9a751408e9de',	'Toys'),
('67d7a36a-eefb-483a-889b-ef88a7cecf2a',	'Books');

CREATE TABLE `emails_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `body` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `owners` (
  `id` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EMAIL_UQ_IDX` (`email`),
  KEY `EMAIL_IDX` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `shops` (
  `id` varchar(36) NOT NULL,
  `owner_id` varchar(36) NOT NULL,
  `category_id` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `open_hours` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `OWNER_ID_IDX` (`owner_id`),
  KEY `CATEGORY_IDX` (`category_id`),
  FULLTEXT KEY `CITY_FT_IDX` (`city`),
  CONSTRAINT `SHOPS_OWNER_FK` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`),
  CONSTRAINT `SHOPS_CATEGORIES_FK` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `offers` (
  `id` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `shop_id` varchar(36) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `SHOP_ID_IDX` (`shop_id`),
  CONSTRAINT `OFFERS_SHOP_FK` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;