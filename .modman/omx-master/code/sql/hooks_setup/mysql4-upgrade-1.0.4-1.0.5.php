<?php
 $installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` = '1.0.5' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1 ;
UPDATE {$this->getTable('omx__settings')} SET `value` = '1' WHERE {$this->getTable('omx__settings')}.`name`='forceMagentoPrices' LIMIT 1 ;
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'autoCreateCoupons', '1');
"); $installer->endSetup();