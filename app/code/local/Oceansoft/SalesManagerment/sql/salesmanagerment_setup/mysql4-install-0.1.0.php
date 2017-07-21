<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/checklist')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `order_id` varchar(50) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `ticket_id` varchar(255) NOT NULL,
  `price` decimal(12,4),
  `percentage` int(11) NOT NULL, 
  `total_earn` decimal(12,4),
  `refund` int(11),
  `note` text,
  `order_date` datetime NULL,
  `created_at` datetime NULL,
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/revenue')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `user_id` int(11) NOT NULL,
  `revenue` int(11),
  `from` datetime NULL,
  `to` datetime NULL,
  `rule` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/salesreport')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `user_id` int(11) NOT NULL,
  `price` decimal(12,4),
  `checklist_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

