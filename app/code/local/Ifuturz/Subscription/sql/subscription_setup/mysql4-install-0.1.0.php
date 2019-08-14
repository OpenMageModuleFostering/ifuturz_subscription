<?php 
/**
 * @package Ifuturz_Subscription
 */
$installer = $this;

$installer->startSetup();


$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('ifuturz_subscription')};
CREATE TABLE {$this->getTable('ifuturz_subscription')} (
  `subscription_id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(50) NULL,
	`email` varchar(50) NULL,		
	`created_at` timestamp default NULL,
	`updated_at` timestamp default NULL,	
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('ifuturz_subscription_category')};
CREATE TABLE {$this->getTable('ifuturz_subscription_category')} (  
  `subscription_fk_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(10) unsigned NOT NULL default '0', 
   KEY `IFUTURZ_SUBSCRIPTION_CATEGORY_SUBS` (`category_id`),
   KEY `IFUTURZ_SUBSCRIPTION_CATEGORY_CATE` (`subscription_fk_id`),
   CONSTRAINT `IFUTURZ_SUBSCRIPTION_CATEGORY_CATE` FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('catalog_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `IFUTURZ_SUBSCRIPTION_CATEGORY_SUBS` FOREIGN KEY (`subscription_fk_id`) REFERENCES {$this->getTable('ifuturz_subscription')} (`subscription_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('subscription_lck')};
CREATE TABLE {$this->getTable('subscription_lck')} ( 	
	`flag` varchar(4),
	`value` ENUM('0','1') DEFAULT '0' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$installer->getTable('subscription_lck')}` VALUES ('LCK','1');
");

$installer->endSetup(); 