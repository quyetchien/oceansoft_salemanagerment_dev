<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('salesmanagerment/checklist')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/checklist')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `order_id` int(11) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `ticket_id` varchar(255) NOT NULL,
  `price` decimal(12,2),
  `sale_percentage` int(11) NOT NULL,
  `note` text,
  `refund` int(11),
  `refund_reason` text,
  `shift` int(11) NOT NULL,
  `order_date` date NULL,
  `user` varchar(255) NOT NULL,
  `total_earn` decimal(12,2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('salesmanagerment/revenue')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/revenue')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `user_id` int(11) NOT NULL,
  `revenue` decimal(12,2),
  `from` datetime NULL,
  `to` datetime NULL,
  `rule` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

