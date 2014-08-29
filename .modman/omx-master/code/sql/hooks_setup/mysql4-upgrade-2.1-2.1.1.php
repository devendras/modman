<?php
$installer = $this; $installer->startSetup(); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` = '2.1.1' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1"); $installer->endSetup(); 