<?php
 $installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` = '1.0.1' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1 ;
INSERT INTO {$this->getTable('omx__settings')} (`id`, `name`, `value`) VALUES (NULL, 'continuityCustomOptionName', 'Continuity');
"); $installer->endSetup();