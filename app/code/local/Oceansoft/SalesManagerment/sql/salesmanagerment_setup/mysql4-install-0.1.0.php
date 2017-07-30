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
  `price` decimal(12,4),
  `refund` int(11),
  `note` text,
  `order_date` datetime NULL,
  `created_at` datetime NULL,
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('salesmanagerment/revenue')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/revenue')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `user_id` int(11) NOT NULL,
  `revenue` int(11),
  `from` datetime NULL,
  `to` datetime NULL,
  `rule` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('salesmanagerment/salesreport')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/salesreport')}` (
  `id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `user_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `total_earn` decimal(12,4),
  `order_id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

