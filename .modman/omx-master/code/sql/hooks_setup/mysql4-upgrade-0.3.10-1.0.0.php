<?php
 $installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` =  '1.0.0' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1 ;
"); $installer->endSetup();