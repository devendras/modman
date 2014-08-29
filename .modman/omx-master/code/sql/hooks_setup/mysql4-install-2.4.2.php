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
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'omxVersion', '2.4.2');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'storeCode', 'Magento');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'defaultKeycode', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'additionalParams', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'forceMagentoPrices', '1');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'refreshInventory', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'continuityCustomOptionName', 'Continuity');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'autoCreateCoupons', '1');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'checkOrderTotalAmount', '1');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'lastResubmissionJobTime', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'resubmissionJobRunCount', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'shippingMethodNonShippable', '(magento non-shippable order)');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'autoProductSendingTime', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'lastProductSendingJobTime', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'productSendingJobRunCount', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'forceProductSending', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'useOmxGiftCertificates', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'connectorConfigured', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'useOmxTaxCalculation', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'omxTaxMagentoFallbackMessage', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'omxTaxOmxFallbackMessage', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'useOmxMarketingPolicies', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'sendPaypalBillingAgreementID', '0');

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

"); $installer->installEntities(); $installer->addAttribute('invoice', 'base_omx_gift_certificates_amount', array('type'=>'decimal')); $installer->addAttribute('invoice', 'omx_gift_certificates_amount', array('type'=>'decimal')); $installer->addAttribute('invoice', 'omx_tax_used', array('type'=>'int')); $installer->addAttribute('invoice', 'omx_pricing', array('type'=>'text')); $installer->addAttribute('quote_address', 'base_omx_gift_certificates_amount_used', array('type'=>'decimal')); $installer->endSetup(); 