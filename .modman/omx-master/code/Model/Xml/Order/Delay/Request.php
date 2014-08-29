<?php

class Omx_Hooks_Model_Xml_Order_Delay_Request extends Omx_Hooks_Model_Xml_Abstract
{
    protected $_order;
    protected $_root;
    protected $_data = array();

    public function __construct()
    {
        $args = func_get_args();
        parent::__construct();
        $this->createRootElement();
        if ($args[0][0] instanceof Omx_Hooks_Model_Order) {
            $this->_order = $args[0][0];
        }
        if (is_array($args[0][1])) {
            $this->_data = $args[0][1];
        }
        $this->appendUdiParams();
    }

    protected function createRootElement()
    {
        $rootElement        = $this->createSafeElement('OrderWaitDateUpdateRequest');
        $rootVersionElement = $this->xml->createAttribute('version');
        $rootElement->appendChild($rootVersionElement);
        $rootVersionValue = $this->xml->createTextNode('1.00');
        $rootVersionElement->appendChild($rootVersionValue);
        $this->xml->appendChild($rootElement);
        $this->_root = $rootElement;
    }

    protected function appendUdiParams()
    {
        $udiParameter             = $this->_root->appendChild($this->createSafeElement('UDIParameter'));
        $orderNumberElement       = $this->createSafeElement('Parameter', $this->_order->orderNumber);
        $orderNumberAttributeText = $this->xml->createTextNode('OrderNumber');
        $orderNumberKeyAttribute  = $this->xml->createAttribute('key');
        $orderNumberKeyAttribute->appendChild($orderNumberAttributeText);
        $orderNumberElement->appendChild($orderNumberKeyAttribute);
        $udiParameter->appendChild($orderNumberElement);
        $lineNumberListElement       = $this->createSafeElement('Parameter', $this->_data['lineNumberList']);
        $lineNumberListAttributeText = $this->xml->createTextNode('LineNumberList');
        $lineNumberListAttribute     = $this->xml->createAttribute('key');
        $lineNumberListAttribute->appendChild($lineNumberListAttributeText);
        $lineNumberListElement->appendChild($lineNumberListAttribute);
        $udiParameter->appendChild($lineNumberListElement);
        $newWaitDateElement       = $this->createSafeElement('Parameter', $this->_data['newWaitDate']);
        $newWaitDateAttributeText = $this->xml->createTextNode('NewWaitDate');
        $newWaitDateAttribute     = $this->xml->createAttribute('key');
        $newWaitDateAttribute->appendChild($newWaitDateAttributeText);
        $newWaitDateElement->appendChild($newWaitDateAttribute);
        $udiParameter->appendChild($newWaitDateElement);
    }
}
