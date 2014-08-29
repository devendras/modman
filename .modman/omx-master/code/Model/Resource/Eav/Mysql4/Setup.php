<?php
 class Omx_Hooks_Model_Resource_Eav_Mysql4_Setup extends Mage_Sales_Model_Mysql4_Setup { public function getDefaultEntities() { return array( 'catalog_product' => array( 'entity_model' => 'catalog/product', 'attribute_model' => 'catalog/resource_eav_attribute', 'table' => 'catalog/product', 'additional_attribute_table' => 'catalog/eav_attribute', 'entity_attribute_collection' => 'catalog/product_attribute_collection', 'attributes' => array( 'omx_iir_timestamp' => array( 'label' => 'Omx IIR timestamp', 'type' => 'datetime', 'input' => 'date', 'default' => '', 'class' => 'validate-date', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'table' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_last_checked' => array( 'type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'label' => 'Last Checked in OMX', 'note' => 'Last call to ItemInformationRequest (ItIR100)', 'input' => 'date', 'class' => 'validate-date', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_product_exists' => array( 'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Product exists on OMX', 'input' => 'boolean', 'class' => '', 'source' => 'eav/entity_attribute_source_boolean', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '0', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_details_match' => array( 'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Product Details match in OMX', 'input' => 'boolean', 'class' => '', 'source' => 'eav/entity_attribute_source_boolean', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '0', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_last_pushed' => array( 'type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'label' => 'Last Pushed to OMX', 'input' => 'date', 'class' => 'validate-date', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_last_updated' => array( 'type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'label' => 'Last Updated in OMX', 'input' => 'date', 'class' => 'validate-date', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_last_inventory_check' => array( 'type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'label' => 'Last OMX Inventory check', 'input' => 'date', 'class' => 'validate-date', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_last_received_from_omx' => array( 'type' => 'datetime', 'backend' => 'eav/entity_attribute_backend_datetime', 'frontend' => '', 'label' => 'Last time data was received from OMX', 'input' => 'date', 'class' => 'validate-date', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_standing_order_configs' => array( 'type' => 'text', 'backend' => '', 'frontend' => '', 'label' => 'Standing Order Configurations from OMX', 'input' => 'textarea', 'class' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ), 'omx_payment_plan_infos' => array( 'type' => 'text', 'backend' => '', 'frontend' => '', 'label' => 'Payment Plan Informations from OMX', 'input' => 'textarea', 'class' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'visible_in_advanced_search' => false, 'unique' => false, 'apply_to' => 'simple' ) ) ), 'quote' => array( 'entity_model' => 'quote', 'table' => 'sales/quote', 'attributes' => array( 'omx_gift_certificates' => array('type' => 'text'), 'base_omx_gift_certificates_amount' => array('type' => 'decimal'), 'base_omx_gift_certificates_amount_used' => array('type' => 'decimal'), 'omx_tax_used'=>array('type' => 'int'), 'omx_pricing' => array('type' => 'text'), 'omx_trigger_price_calc'=> array('type'=>'int'), 'omx_order_keycode' => array('type' => 'varchar'), ), ), 'order' => array( 'entity_model' => 'order', 'table' => 'sales/order', 'increment_model'=>'eav/entity_increment_numeric', 'increment_per_store'=>true, 'backend_prefix'=>'sales_entity/order_attribute_backend', 'attributes' => array( 'omx_order_keycode' => array( 'label' => 'Omx Order Keycode', 'type' => 'varchar', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true ), 'omx_order_call_to_action' => array( 'label' => 'Call To Action', 'type' => 'varchar', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true ), 'omx_order_vendor' => array( 'label' => 'Vendor', 'type' => 'varchar', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true ), 'omx_order_custom_fields' => array( 'label' => 'Omx Custom Fields', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_order_id' => array( 'label' => 'Omx Order ID', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_order_paymentplan_id' => array( 'label' => 'Omx Order Payment Plan', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_order_storecode' => array( 'label' => 'Omx Order Store Code', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_gift_certificates' => array( 'label' => 'Omx Gift Certificates', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'base_omx_gift_certificates_amount_used' => array( 'label' => 'Omx Gift Certificates', 'type' => 'decimal', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_gift_certificates_amount_used' => array( 'label' => 'Omx Gift Certificates Amount Used', 'type' => 'decimal', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_tax_used' => array( 'label' => 'Omx Tax Calculation Used', 'type' => 'int', 'is_null' => true, 'default' => NULL, 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_pricing' => array( 'label' => 'Omx Pricing Data', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), ) ), 'order_item' => array( 'entity_model' => 'sales/order_item', 'table' => 'sales/order_entity', 'attributes' => array( 'omx_extra_customizations' => array( 'label' => 'Omx Extra Item Customizations', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_paymentplan_id' => array( 'label' => 'Omx Item Payment Plan', 'type' => 'text', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ), 'omx_wait_date' => array( 'label' => 'Omx Order Line Wait Date', 'type' => 'datetime', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, ) ) ), 'customer' => array( 'entity_model' =>'customer/customer', 'table' => 'customer/entity', 'increment_model' => 'eav/entity_increment_numeric', 'attribute_model' => 'customer/attribute', 'additional_attribute_table' => 'customer/eav_attribute', 'entity_attribute_collection' => 'customer/attribute_collection', 'increment_per_store' => false, 'attributes' => array( 'omx_customer_number' => array( 'label' => 'Omx Customer Number', 'type' => 'int', 'visible' => true, 'required' => false, 'user_defined' => true ), 'omx_tax_exempt' => array( 'label' => 'Omx Tax Exempt Flag', 'type' => 'int', 'input' => 'boolean', 'visible' => true, 'required' => false, 'user_defined' => true, 'default' => 0 ) ) ), 'order_payment' => array( 'entity_model' => 'sales/order_payment', 'attribute_model' => '', 'table' => 'sales/order_entity', 'attributes' => array( 'omx_order_payment_type' => array( 'label' => 'Omx Order Payment Type', 'type' => 'int', 'input' => 'text', 'default' => NULL, 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_paidamount' => array( 'label' => 'Omx Order Payment Amount', 'type' => 'decimal', 'input' => 'text', 'default' => NULL, 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_status' => array( 'label' => 'Omx Order Payment Status', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_transactionid' => array( 'label' => 'Omx Order Payment Transaction ID', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardnumber' => array( 'label' => 'Omx Order Payment Credit Card Number', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardexpdatemonth' => array( 'label' => 'Omx Order Payment Credit Card Expiration Date Month', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardexpdateyear' => array( 'label' => 'Omx Order Payment Credit Card Expiration Date Year', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardissuenumber' => array( 'label' => 'Omx Order Payment Credit Card Issue Number', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardissuedatemonth' => array( 'label' => 'Omx Order Payment Credit Card Issue Date Month', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardissuedateyear' => array( 'label' => 'Omx Order Payment Credit Card Issue Date Year', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardauthcode' => array( 'label' => 'Omx Order Payment Credit Card Authorization Code', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_echeckbankroutingnumber' => array( 'label' => 'Omx Order Payment Electronic Check Bank Routing Number', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_echeckbankaccontnumber' => array( 'label' => 'Omx Order Payment Electronic Check Bank Account Number', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_checknumber' => array( 'label' => 'Omx Order Payment Check Number', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_giftcertificatecode' => array( 'label' => 'Omx Order Payment Gift Certificate Code', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_paypalpayerid' => array( 'label' => 'Omx Order Payment PayPal Payer ID', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_paypalbuyeremail' => array( 'label' => 'Omx Order Payment PayPal Buyer Email', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardavsresultcode' => array( 'label' => 'Omx Order Payment AVS Result Code', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardccvresultcode' => array( 'label' => 'Omx Order Payment CCV Result Code', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardtoken' => array( 'label' => 'Omx Order Payment Credit Card Token', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardtype' => array( 'label' => 'Omx Order Payment Credit Card Type', 'type' => 'int', 'input' => 'text', 'default' => NULL, 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardfirst6digits' => array( 'label' => 'Omx Order Payment Credit Card First 6 Digits', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ), 'omx_order_payment_creditcardlast4digits' => array( 'label' => 'Omx Order Payment Credit Card Last 4 Digits', 'type' => 'varchar', 'input' => 'text', 'default' => '', 'class' => '', 'backend' => '', 'frontend' => '', 'source' => '', 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, 'visible' => true, 'required' => false, 'user_defined' => true, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => false, 'unique' => false, ) ) ) ); } } 