<?php
$installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` = '1.1.2' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1 ;
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'autoProductSendingTime', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'lastProductSendingJobTime', '');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'productSendingJobRunCount', '0');
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'forceProductSending', '0');
"); $installer->endSetup(); 