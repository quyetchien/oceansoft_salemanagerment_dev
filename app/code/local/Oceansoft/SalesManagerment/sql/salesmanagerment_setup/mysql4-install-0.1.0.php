<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/checklist')}` (
  `checklist_id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `order_id` varchar(50) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `ticket_id` varchar(255) NOT NULL,
  `price` decimal(12,4),
  `refund` int(11),
  `note` text,
  `order_date` datetime NULL,
  `created_at` datetime NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/checklistgroup')}` (
  `group_id` int(11) NOT NULL PRIMARY KEY auto_increment,
  `checklist_id` int(11),
  `sale_name` varchar(255) NOT NULL,
  `value` int(11),
  FOREIGN KEY (checklist_id) REFERENCES {$this->getTable('salesmanagerment/checklist')}(checklist_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

