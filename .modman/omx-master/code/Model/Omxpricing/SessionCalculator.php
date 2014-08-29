<?php
 class Omx_Hooks_Model_Omxpricing_SessionCalculator extends Omx_Hooks_Model_Omxpricing_Calculator { private $_address = null; private $_propertiesToSave = array( 'pricedQuoteId', '_requestXml', '_responseXml', '_taxLoaded', '_discountLoaded', 'itemsTax', 'couponTax', 'subtotalTaxAmount', 'totalTax', 'omxTaxSettings', 'shippingTax', 'discounts', 'freeItems', 'totalDiscountAmount' ); public function __construct(Mage_Sales_Model_Quote_Address $address) { $this->_address = $address; parent::__construct($address->getQuote()); } public function getPricing($quote = null, $type = parent::PRICING_TYPE_COMPLETE) { if ($quote instanceof Mage_Sales_Model_Quote) { $this->_quote = $quote; } if ($this->_pricingBaseDataHasChanged() ) { parent::getPricing($quote, parent::PRICING_TYPE_COMPLETE); $this->_writeBaseDataToSession(); $this->_writeObjectDataToSession(); } else { if($this->_validateSessionData()) { $this->_writeSessionDataToObject(); } else { parent::getPricing($quote, parent::PRICING_TYPE_COMPLETE); $this->_writeBaseDataToSession(); $this->_writeObjectDataToSession(); } } } public function getTax($quote = null) { $this->getPricing($quote); } public function getDiscount($quote = null) { $this->getPricing($quote); } public function getResponseXml() { return $this->_responseXml; } public function hasAddressError() { if($this->_responseXPath != null) { $errorElements = $this->_responseXPath->query('//*/ErrorData/Error'); foreach($errorElements as $errorElement) { if (strstr(mb_strtolower($errorElement->nodeValue), 'invalid shipping address') || strstr(mb_strtolower($errorElement->nodeValue), 'invalid shippping address')) { return true; } } } return false; } private function _pricingBaseDataHasChanged() { $omxSessionData = Mage::getSingleton("core/session")->getData("omx"); if (!is_array($omxSessionData)) { return true; } if(!array_key_exists('pricingBaseData', $omxSessionData)) { return true; } if (!is_array($omxSessionData['pricingBaseData'])) { return true; } foreach ($omxSessionData['pricingBaseData'] as $key=>$value) { switch ($key) { case 'address_id': if ($this->_address->getAddressId() != $value) return true; break; case 'postcode' : if ($this->_address->getPostcode() != $value) return true; break; case 'region': if ($this->_address->getRegion() != $value) return true; break; case 'country_id': if($this->_address->getCountryId() != $value) return true; break; case 'omx_customer_number': if ($this->_address->getQuote()->getCustomerOmxCustomerNumber() != $value) return true; break; case 'quote_id' : if($this->_address->getQuoteId() != $value) return true; break; case 'base_subtotal' : if ($this->_address->getBaseSubtotal() != $value) return true; break; case 'base_discount_amount' : if ($this->_address->getBaseDiscountAmount() != $value) return true; break; case 'base_shipping_amount' : if ($this->_address->getBaseShippingAmount() != $value) return true; break; case 'items' : foreach ($value as $id=>$storedItem) { $item = $this->_address->getQuote()->getItemById($id); if (!$item instanceof Mage_Sales_Model_Quote_Item ) { return true; } foreach ($storedItem as $itemKey=>$itemData) { switch ($itemKey) { case 'qty': if ($item->getQty() != $itemData) return true; break; case 'sku' : if ($item->getSku() != $itemData) return true; break; case 'base_price' : if ($item->getBasePrice() != $itemData) return true; break; case 'base_discount_amount' : if ($item->getBaseDicountAmount() != $itemData) return true; break; case 'base_row_total' : if ($item->getBaseRowTotal() != $itemData) return true; break; } } } if (count($value) != count($this->_address->getAllItems())) { return true; } break; default: return true; break; } } return false; } private function _validateSessionData() { $omxSessionData = Mage::getSingleton("core/session")->getData("omx"); $values = unserialize($omxSessionData['pricingCalculator']); $valid = false; if(is_array($values)) { if( array_key_exists('_taxLoaded', $values) && array_key_exists('_discountLoaded', $values) && array_key_exists('pricedQuoteId', $values) ) { $valid = ( $values['_taxLoaded'] && $values['_discountLoaded'] && $values['pricedQuoteId'] == $this->_quote->getId() ) ? true : false; } } return $valid; } private function _writeSessionDataToObject() { $omxSessionData = Mage::getSingleton("core/session")->getData("omx"); try { $values = unserialize($omxSessionData['pricingCalculator']); foreach ($values as $key=>$val) { $this->{$key} = $val; } } catch (Exception $e) { parent::getPricing(); } } private function _writeBaseDataToSession() { $compareValues = array(); $compareValues['address_id'] = $this->_address->getAddressId(); $compareValues['postcode'] = $this->_address->getPostcode(); $compareValues['region'] = $this->_address->getRegion(); $compareValues['country_id'] = $this->_address->getCountryId(); $compareValues['omx_customer_number'] = $this->_address->getQuote()->getCustomerOmxCustomerNumber(); $compareValues['quote_id'] = $this->_address->getQuoteId(); $compareValues['base_subtotal'] = $this->_address->getBaseSubtotal(); $compareValues['base_discount_amount'] = $this->_address->getBaseDiscountAmount(); $compareValues['base_shipping_amount'] = $this->_address->getBaseShippingAmount(); $compareValues['items'] = array(); $items = $this->_address->getQuote()->getAllItems(); if (is_array($items) && sizeof($items) > 0 ) { foreach ($items as $item) { $compareValues['items'][$item->getItemId()] = array( 'qty' => $item->getQty(), 'sku' => $item->getSku(), 'base_price' => $item->getBasePrice(), 'base_discount_amount'=>$item->getBaseDicountAmount(), 'base_row_total' =>$item->getBaseRowTotal(), ); } } $omxSessionData = Mage::getSingleton("core/session")->getData("omx"); $omxSessionData['pricingBaseData'] = $compareValues; Mage::getSingleton("core/session")->setData("omx", $omxSessionData); } private function _writeObjectDataToSession() { $properties = array(); foreach ($this->_propertiesToSave as $varName) { $properties[$varName] = $this->{$varName}; } $omxSessionData = Mage::getSingleton("core/session")->getData("omx"); $omxSessionData['pricingCalculator'] = serialize($properties); Mage::getSingleton("core/session")->setData("omx", $omxSessionData); } }