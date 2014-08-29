<?php
 $installer = $this; $installer->startSetup(); $installer->run("
DROP TABLE IF EXISTS {$this->getTable('omx__settings')};

CREATE TABLE {$this->getTable('omx__settings')} (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 40 ) NOT NULL ,
`value` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_bin;

INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'connectorEnabled', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'debugMode', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'udiAuthToken', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'udiUrl', 'https://api.omx.ordermotion.com/');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'omxVersion', '1.0.5');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'storeCode', 'Magento');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'defaultKeycode', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'additionalParams', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'forceMagentoPrices', '1');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'refreshInventory', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'continuityCustomOptionName', 'Continuity');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'autoCreateCoupons', '1');

DROP TABLE IF EXISTS {$this->getTable('omx__data')};

CREATE TABLE {$this->getTable('omx__data')} (
`omx_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`xml_string` TEXT NOT NULL ,
`order_id` INT NOT NULL ,
`status` VARCHAR( 255 ) NOT NULL ,
`response` TEXT NOT NULL ,
`timestamp` TIMESTAMP NOT NULL,
`resubmited` INT NOT NULL DEFAULT '0',
`parent_id` INT NOT NULL DEFAULT '0'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_bin;

DROP TABLE IF EXISTS {$this->getTable('omx__logs')};
CREATE TABLE {$this->getTable('omx__logs')} (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`timestamp` TIMESTAMP NOT NULL,
`request` TEXT NOT NULL ,
`response` TEXT NOT NULL ,
`comment` VARCHAR( 512 ) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_bin;

"); $installer->installEntities(); $installer->endSetup(); 