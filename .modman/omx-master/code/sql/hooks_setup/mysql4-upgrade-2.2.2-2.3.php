<?php
$installer = $this; $installer->startSetup(); $installer->addAttribute('customer', 'omx_tax_exempt', array( 'label' => 'Omx Tax Exempt Flag', 'type' => 'int', 'input' => 'boolean', 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => 0 ) ); $installer->run("
UPDATE {$this->getTable('omx__settings')} SET `value` = '2.3' WHERE {$this->getTable('omx__settings')}.`name`='omxVersion' LIMIT 1;
"); $installer->endSetup(); 