<?php
 class Omx_Hooks_Model_Omxpricing_Sales_Order_Invoice_Total_Omxdiscount extends Mage_Sales_Model_Order_Invoice_Total_Abstract { public function collect(Mage_Sales_Model_Order_Invoice $invoice) { if (Mage::helper('hooks/data')->getSettings('useOmxMarketingPolicies') == 1) { $order = $invoice->getOrder(); $invoice->setBaseGrandTotal($order->getBaseGrandTotal()); $invoice->setGrandTotal($order->getGrandTotal()); $invoice->setOmxTaxUsed($order->getOmxTaxUsed()); } $invoice->setOmxPricing($order->getOmxPricing()); return $this; } }