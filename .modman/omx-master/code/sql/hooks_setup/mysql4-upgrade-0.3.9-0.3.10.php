<?php
 $installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` =  '0.3.10' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1 ;
"); $installer->endSetup();