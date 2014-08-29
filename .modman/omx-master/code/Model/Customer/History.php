<?php
 class Omx_Hooks_Model_Customer_History { private $_connector; public $omx_customer_number; public $customer; public $orders = array(); public $last_orders = array(); private $_all_omx_orders = array(); private $_pending_orders = array(); public $limit; public $responseXml; private $_xpath; private $_details; private function _connectivity() { $this->_connector = Mage::getModel('hooks/connector'); $moduleConfigured = $this->_connector->isConfigured(); $connectorEnabled = $this->_connector->connectorEnabled; if ($moduleConfigured && $connectorEnabled) { return true; } return false; } public function __construct (Mage_Customer_Model_Customer $customer, $limit = 5, $details = null) { if ($this->_connectivity()) { $this->limit = $limit; $this->customer = $customer; $this->_details = $details; if ($this->customer instanceof Mage_Customer_Model_Customer) { $magentoOrders = Mage::getModel('sales/order')->getCollection() ->addAttributeToSelect('*') ->addAttributeToSort('created_at', 'desc') ->addFieldToFilter('customer_id',array('eq' => $this->customer->getId())); $this->orders = $magentoOrders->getData(); $this->omx_customer_number = $this->customer->getOmxCustomerNumber(); if (!$this->omx_customer_number) { $orderIncrementId = $this->orders[0]['increment_id']; if($orderIncrementId) { $this->omx_customer_number = $this->getOmxCustNumber($orderIncrementId); } if(!$this->omx_customer_number && count($this->orders) > 1) { $orderIncrementId = $this->orders[count($this->orders)-1]['increment_id']; $this->omx_customer_number = $this->getOmxCustNumber($orderIncrementId); } $this->customer->setOmxCustomerNumber($this->omx_customer_number); $this->customer->getResource()->saveAttribute($this->customer, "omx_customer_number"); } if ($this->omx_customer_number) { $xmlObject = Mage::getModel('hooks/xml_customer_history_request', $this->omx_customer_number); if ($xmlObject instanceof Omx_Hooks_Model_Xml_Customer_History_Request) { $omx_now = Zend_Date::now(); $omx_now->setTimezone('America/New_York'); $credentials = array( 'xml' => $xmlObject ); $this->responseXml = $this->_connector->callOmx(Omx_Hooks_Model_Connector::CALL_TYPE_CUHR, $credentials); $this->_xpath = new DOMXPath( $this->responseXml); } } $this->last_orders = $this->lastOrders(); } } return false; } private function lastOrders () { $result = array(); $this->_all_omx_orders = $this->getAllOmxOrders(); $this->_pending_orders = $this->getPendingOrders(); $cumulated_orders = array_merge($this->_all_omx_orders, $this->_pending_orders); $helper_array = array(); foreach ($cumulated_orders as $key=> $order){ $helper_array[$key] = $order['orderDate']; } asort($helper_array); $helper_array = array_reverse($helper_array, true); $time_sorted_orders = array(); foreach ($helper_array as $key => $time ) { $time_sorted_orders[] = $cumulated_orders[$key]; } if (count($time_sorted_orders)) { $i = 0; foreach ($time_sorted_orders as $order) { if ($i < $this->limit) { $result[] = $order; ++$i; } } } return $result; } private function parseOrder ($order) { $orderNumber = $order->getAttribute('orderNumber'); $orderStatus = $this->getElementValue('OrderStatus', $order); $result = array( 'orderNumber' => $orderNumber, 'orderStatus' => $orderStatus ); $magentoOrderId = $this->getElementValue('OrderID', $order); $magentoOrder = Mage::getModel('sales/order')->loadByIncrementId($magentoOrderId); $storeCode = $this->getElementValue('StoreCode', $order); if ( $magentoOrder instanceof Mage_Sales_Model_Order && $magentoOrder->getIncrementId() && $magentoOrder->getOmxOrderId() <= 1 && $this->_connector->storeCode == $storeCode ) { $magentoOrder->setOmxOrderId($orderNumber); $magentoOrder->getResource()->saveAttribute($magentoOrder, "omx_order_id"); } $originTypeId = $this->getElementValue('OriginType', $order); switch ($originTypeId) { case 0 : default : $originType = 'Unknown'; break; case 1 : $originType = 'Mail'; break; case 2 : $originType = 'Phone'; break; case 3 : $originType = 'Internet'; break; case 4 : $originType = 'Walk-In / POS'; break; case 5 : $originType = 'Mobile'; break; case 6 : $originType = 'Mail'; break; } $result['mageOrderNumber'] = ($this->_connector->storeCode == $storeCode)? $magentoOrderId : ''; $result['originType'] = $originType; $result['storeCode'] = $storeCode; $result['keycode'] = $this->getElementValue('Keycode', $order); $result['orderDate'] = $this->getElementValue('OrderDate', $order); $result['keyedDate'] = $this->getElementValue('KeyedDate', $order); $result['orderAmount'] = $this->getElementValue('TotalAmount', $order); $result['taxAmount'] = $this->getElementValue('Tax', $order); $result['shippingMethodName']= $this->getElementValue('Method', $order); $result['shippingAmount'] = $this->getElementValue('ShippingAmount', $order); $result['primaryCreditCardType'] = $this->getElementValue('CreditCardName', $order); $result['paidAmount'] = $this->getElementValue('TotalPaid', $order); $result['lastCreditCardStatus'] = $this->getElementValue('LastCreditCardStatus', $order); $addresses = $order->getElementsByTagName('Address'); foreach ($addresses as $address) { $addressType = $address->getAttribute('type'); switch ($addressType) { case 'ShipTo': $result['shippingAddress'] = $this->getElementValue('FullAddress', $address); break; case 'BillTo': $result['billingAddress'] = $this->getElementValue('FullAddress', $address); break; default: break; } } $paymentElement = $order->getElementsByTagName('Payment'); if ($paymentElement instanceof DOMNodeList && $paymentElement->length) { $result['primaryPaymentTypeDescription'] = $paymentElement->item(0)->getAttribute('description'); } if ($this->_details) { $lineItemElements = $order->getElementsByTagName('LineItem'); foreach ($lineItemElements as $line) { $lineNumber = $line->getAttribute('lineNumber'); $parentLineNumber = $line->getAttribute('parentLineNumber'); $priceValue = $this->getElementValue('Price', $line); $priceType = ($priceValue)? $line->getElementsByTagName('Price')->item(0)->getAttribute('priceType'): ''; $taxValue = $this->getElementValue('Tax', $line); $taxPercent = ($taxValue)? $line->getElementsByTagName('Tax')->item(0)->getAttribute('taxPercent'): ''; $lineStatusElement = $line->getElementsByTagName('LineStatus'); if ($lineStatusElement->length) { $status = $lineStatusElement->item(0)->getAttribute('text'); $statusDate = $lineStatusElement->item(0)->getAttribute('date'); } $quantityValue = $this->getElementValue('Quantity', $line); $quantityElement = $line->getElementsByTagName('Quantity'); $includedBonusQuantity =($quantityElement->length) ? $includedBonusQuantity = $quantityElement->item(0)->getAttribute('bonus') : '' ; $result['lines'][$lineNumber] = array( 'lineNumber' => $lineNumber, 'itemCode' => $this->getElementValue('ItemCode', $line), 'productName' => $this->getElementValue('ProductName', $line), 'optionsText' => '(= combo of dimension data and customization)', 'quantity' => $quantityValue, 'priceType' => $priceType, 'price' => $priceValue, 'effectiveUnitPrice' => $this->getElementValue('EffectiveUnitPrice', $line), 'taxPercent' => $taxPercent, 'tax' => $taxValue, 'totalPrice' => $this->getElementValue('TotalPrice',$line), 'status' => $status, 'statusDate' => $statusDate, 'info' => $this->getElementValue('Info', $line), 'includedBonusQuantity' => $includedBonusQuantity, 'options' => array( 'dimensionLabel1' => $this->getElementValue('DimensionLabel1', $line), 'dimensionValue1' => $this->getElementValue('DimensionValue1', $line), 'dimensionLabel2' => $this->getElementValue('DimensionLabel2', $line), 'dimensionValue2' => $this->getElementValue('DimensionValue2', $line), 'dimensionLabel3' => $this->getElementValue('DimensionLabel3', $line), 'dimensionValue3' => $this->getElementValue('DimensionValue3', $line), ), 'parentLineNumber' => $parentLineNumber ); $itemCustomizations = $line->getElementsByTagName('CustomizationField'); if ($itemCustomizations->length) { foreach ($itemCustomizations as $customizationField){ $combinedCustomizationValue = ''; $customizationValuesElements = $customizationField->getElementsByTagName('Value'); $values = $customizationValuesElements->length; if ($values) { $i = 1; foreach ($customizationValuesElements as $value) { $combinedCustomizationValue .= $value->nodeValue; if( $i < $values) { $combinedCustomizationValue .","; } ++$i; } } $result['lines'][$lineNumber]['customizations'][] = array( 'customizationName' => $customizationField->getAttribute('fieldName'), 'customizationValue' => $combinedCustomizationValue ); } } $originalLineNumber = $line->getAttribute('originalLineNumber'); $path = "//LineItem[@lineNumber='". $originalLineNumber ."']/ItemCode"; $originalLineItemCode = $this->_xpath->query($path); $originalItemReplacedCode = ''; if($originalLineItemCode->length){ $originalItemReplacedCode = $originalLineItemCode->item(0)->nodeValue; } $result['lines'][$lineNumber]['originalItemReplaced'] = $originalItemReplacedCode; $parentLineNumber = $line->getAttribute('parentLineNumber'); $orderLineAdditionType = $line->getAttribute('orderLineAdditionType'); switch ($orderLineAdditionType) { case '5': case '15': $parentLineReason = 'Bundle'; break; case '6': case '16': $parentLineReason = 'Standing Order'; break; case '7': case '17': $parentLineReason = 'Marketing Kit'; break; default: $parentLineReason = ''; break; } $result['lines'][$lineNumber]['parentLineReason'] = $parentLineReason; } } return $result; } private function getElementValue($elementName, DOMElement $parent = null) { $element = (!$parent instanceof DOMElement) ? $this->responseXml->getElementsByTagName($elementName) : $parent->getElementsByTagName($elementName); if ($element instanceof DOMNodeList) { if ($element->length){ return $element->item(0)->nodeValue; } } return false; } private function getPendingOrders () { $results = array(); foreach ($this->orders as $order){ $results[$order['increment_id']] = false; foreach ($this->_all_omx_orders as $omxOrder) { if ($omxOrder['mageOrderNumber'] === $order['increment_id']) { $results[$order['increment_id']] = true; } } } $pending_orders = array(); foreach ($results as $key => $value) { if (!$value) { $mageOrder = Mage::getModel('sales/order')->loadByIncrementId($key); $statusCode = ''; $entries = Mage::getModel('hooks/data') ->getCollection() ->addFieldToFilter('order_id',array('eq' => $key)) ->load(); if( $entries->count()){ $firstItem = $entries->getFirstItem(); if ($firstItem instanceof Omx_Hooks_Model_Data) { $omxConnectorDataStatus = $firstItem->getStatus(); if ($omxConnectorDataStatus > 1) { $statusCode = $omxConnectorDataStatus; } } } $status = ($statusCode)? "transmited" : 'just placed'; if($mageOrder->getShippingAddress()) { $shippingAddress = $mageOrder->getShippingAddress()->getFirstname() . " " . $mageOrder->getShippingAddress()->getLastname() . "\n" . $mageOrder->getShippingAddress()->getStreetFull() . "\n" . $mageOrder->getShippingAddress()->getCity() . ", " . $mageOrder->getShippingAddress()->getRegion(). " " . $mageOrder->getShippingAddress()->getPostcode() . "\n" . $mageOrder->getShippingAddress()->getCountry(); } else $shippingAddress = ""; $billingAddress = $mageOrder->getBillingAddress()->getFirstname() . " " . $mageOrder->getBillingAddress()->getLastname() . "\n" . $mageOrder->getBillingAddress()->getStreetFull() . "\n" . $mageOrder->getBillingAddress()->getCity() . ", " . $mageOrder->getBillingAddress()->getRegion(). " " . $mageOrder->getBillingAddress()->getPostcode() . "\n" . $mageOrder->getBillingAddress()->getCountry(); $pending_orders[] = array( 'orderNumber' => $statusCode, 'orderStatus' => $status, 'mageOrderNumber' => $key, 'originType' => 'Internet', 'storeCode' => '', 'keycode' => '', 'orderDate' => $mageOrder->getCreatedAt(), 'keyedDate' => '', 'orderAmount' => $mageOrder->getGrandTotal(), 'taxAmount' => $mageOrder->getTaxAmount(), 'shippingMethodName'=> $mageOrder->getShippingDescription(), 'shippingAmount' => $mageOrder->getBaseShippingAmount(), 'primaryCreditCardType' => '', 'paidAmount' => '', 'lastCreditCardStatus' => '', 'shippingAddress' => $shippingAddress, 'billingAddress' => $billingAddress ); } } return $pending_orders; } private function getAllOmxOrders () { $result = array(); if ($this->responseXml) { $orderElements = $this->responseXml->getElementsByTagName('Order'); if ($orderElements->length) { foreach ($orderElements as $order) { $result[] = $this->parseOrder($order); } } } return $result; } private function getOmxCustNumber($incrementId = NULL) { $omx_customer_number; if($incrementId) { $magentoLastOrder = Mage::getModel('sales/order')->loadByIncrementId($incrementId); if ($magentoLastOrder instanceof Mage_Sales_Model_Order) { $omxOrder = Mage::getModel('hooks/order'); $omxOrder->loadFromOrder($magentoLastOrder); $omx_customer_number = $omxOrder->customerNumber; } } return $omx_customer_number; } }