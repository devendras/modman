<?php
class Omx_Hooks_Block_Omxpricing_Checkout_Onepage_Omxdiscount extends Mage_Checkout_Block_Onepage_Abstract { const UNSELECTED_POLICIES = 'unselected'; const SELECTED_POLICIES = 'selected'; const ALL_POLICIES = 'all'; private $_update = false; protected function _construct() { $this->getCheckout()->setStepData('omxdiscount', array( 'label' => Mage::helper('checkout')->__('Get your free item'), 'is_show' => true )); parent::_construct(); } public function getFreeItemDiscounts() { $quote = Mage::getSingleton('checkout/session')->getQuote(); $freeItemDiscountsData = mage::helper('hooks/omxpricing')->getDiscounts( $quote, Omx_Hooks_Model_Omxpricing_Calculator::OMX_FREE_ITEM_DISCOUNTS ); if (!array_key_exists('selections', $freeItemDiscountsData)) { return array(); } return $freeItemDiscountsData['selections']; } public function getFreeItemSelections($section = self::ALL_POLICIES) { $freeItemDiscounts = array(); $selections = array(); $unselected = array(); $selected = array(); if(Mage::helper('hooks/data')->getSettings('useOmxMarketingPolicies')== 1) { $freeItemDiscounts = Mage::helper('hooks/omxpricing')->getDiscounts( $this->getQuote(), Omx_Hooks_Model_Omxpricing_Calculator::OMX_FREE_ITEM_DISCOUNTS ); if(array_key_exists('selections', $freeItemDiscounts)) { if(is_array($freeItemDiscounts['selections'])) { $selections = $freeItemDiscounts['selections']; } } foreach($selections as $policyId => $itemCode) { if (array_key_exists($policyId, $freeItemDiscounts['selectedItems'])) { $selected[$policyId] = $selections[$policyId]; $selected[$policyId]['selectedItem'] = $freeItemDiscounts['selectedItems'][$policyId]; } else { $unselected[$policyId] = $selections[$policyId]; } } } switch ($section) { case self::UNSELECTED_POLICIES : return $unselected; case self::SELECTED_POLICIES : return $selected; default : return $selections; } } public function setUpdate($var = null) { $this->_update = $var; return $this; } public function isUpdate() { return $this->_update; } } 