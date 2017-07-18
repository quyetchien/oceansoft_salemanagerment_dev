<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('salesmanagerment/checklist')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `order_id` varchar(50) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `ticket_id` varchar(255) NOT NULL,
  `price` decimal(12,4),
  `refund` int(11),
  `group` varchar(255) NOT NULL,
  `order_date` datetime NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

