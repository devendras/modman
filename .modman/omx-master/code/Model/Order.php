<?php

class Omx_Hooks_Model_Order
{
    private $_xml;
    private $_xpath;
    public $mageOrderNumber;
    public $orderNumber;
    private $_originTypeId;
    public $originType;
    public $storeCode;
    public $keycode;
    public $customerNumber;
    public $orderDate;
    public $keyedDate;
    public $orderAmount;
    public $taxAmount;
    public $trackingNumbers;
    public $shippingMethodName;
    public $shippingMethodCode;
    public $shippingCarrierName;
    public $billingName;
    public $billingCompany;
    public $billingPhone;
    public $billingEmail;
    public $shippingName;
    public $shippingCompany;
    public $shippingPhone;
    public $shippingEmail;
    public $shippingAmount;
    public $shippingAddress;
    public $billingAddress;
    public $primaryPaymentTypeDescription;
    public $primaryCreditCardType;
    public $paidAmount;
    public $lastCreditCardStatus;
    public $lines = array();
    public $is_loaded;
    public function __construct()

    {
        $moduleConfigured = Mage::getModel('hooks/connector')->isConfigured();
        $connectorEnabled = Mage::getModel('hooks/connector')->connectorEnabled;
        if ($connectorEnabled && $moduleConfigured) {
            $this->is_loaded = false;
            return $this;
        } else {
            return false;
        }
    }
    public function loadFromHash($hash)
    {
        $xmlResult = Omx_Hooks_Model_Order_Transfer::getFromHash($hash);
        if ($xmlResult instanceof DOMDocument) {
            $this->loadXml($xmlResult);
            return $this;
        } else {
            return false;
        }
    }
    public function loadFromOrder(Mage_Sales_Model_Order $order)
    {
        $xmlResult = Omx_Hooks_Model_Order_Transfer::getFromOrder($order);
        if ($xmlResult instanceof DOMDocument) {
            $this->loadXml($xmlResult);
            return $this;
        } else {
            return false;
        }
    }
    private function loadXml(DOMDocument $xmlResult)
    {
        $this->_xml            = $xmlResult;
        $this->_xpath          = new DOMXPath($this->_xml);
        $this->mageOrderNumber = $this->getElementValue('OrderID');
        $this->orderNumber     = $this->getElementValue('OrderNumber');
        $this->_originTypeId   = $this->getElementValue('OriginType');
        switch ($this->_originTypeId) {
            case 0:
            default:
                $this->originType = 'Unknown';
                break;
            case 1:
                $this->originType = 'Mail';
                break;
            case 2:
                $this->originType = 'Phone';
                break;
            case 3:
                $this->originType = 'Internet';
                break;
            case 4:
                $this->originType = 'Walk-In / POS';
                break;
            case 5:
                $this->originType = 'Mobile';
                break;
            case 6:
                $this->originType = 'Mail';
                break;
        }
        $this->storeCode = $this->getElementValue('StoreCode');
        $this->keycode   = $this->getElementValue('Keycode');
        $customerElement = $this->_xml->getElementsByTagName('Customer');
        if ($customerElement->length > 0) {
            $this->customerNumber = $customerElement->item(0)->getAttribute('number');
        }
        $this->orderDate   = $this->getElementValue('OrderDate');
        $this->keyedDate   = $this->getElementValue('KeyedDate');
        $this->orderAmount = $this->getElementValue('TotalAmount');
        $this->taxAmount   = $this->getElementValue('Tax');
        $shipment          = $this->_xml->getElementsByTagName('Shipment')->item(0);
        if ($shipment instanceof DOMElement) {
            $TnString              = $this->getElementValue('TrackingNumber', $shipment);
            $this->trackingNumbers = explode("|", $TnString);
        }
        $this->shippingMethodName = $this->getElementValue('Method');
        $meth                     = $this->_xml->getElementsByTagName('Method');
        if ($meth->item(0) instanceof DOMElement) {
            $this->shippingMethodCode  = $meth->item(0)->getAttribute('code');
            $this->shippingCarrierName = $meth->item(0)->getAttribute('carrierName');
        }
        $this->shippingAmount = $this->getElementValue('ShippingAmount');
        $addresses            = $this->_xml->getElementsByTagName('Address');
        foreach ($addresses as $address) {
            $addressType = $address->getAttribute('type');
            switch ($addressType) {
                case 'ShipTo':
                    $this->shippingAddress = $this->getElementValue('FullAddress', $address);
                    $this->shippingName    = $this->getElementValue('FullName', $address);
                    $this->shippingCompany = $this->getElementValue('Company', $address);
                    $this->shippingPhone   = $this->getElementValue('FullPhone', $address);
                    $this->shippingEmail   = $this->getElementValue('Email', $address);
                    break;
                case 'BillTo':
                    $this->billingAddress = $this->getElementValue('FullAddress', $address);
                    $this->billingName    = $this->getElementValue('FullName', $address);
                    $this->billingCompany = $this->getElementValue('Company', $address);
                    $this->billingPhone   = $this->getElementValue('PhoneNumber', $address);
                    $this->billingEmail   = $this->getElementValue('Email', $address);
                    break;
                default:
                    break;
            }
        }
        $paymentElement = $this->_xml->getElementsByTagName('Payment');
        if ($paymentElement instanceof DOMNodeList && $paymentElement->length) {
            $this->primaryPaymentTypeDescription = $paymentElement->item(0)->getAttribute('description');
        }
        $this->primaryCreditCardType = $this->getElementValue('CreditCardName');
        $this->paidAmount            = $this->getElementValue('TotalPaid');
        $this->lastCreditCardStatus  = $this->getElementValue('LastCreditCardStatus');
        $lineItemElements            = $this->_xml->getElementsByTagName('LineItem');
        foreach ($lineItemElements as $line) {
            $lineNumber        = $line->getAttribute('lineNumber');
            $parentLineNumber  = $line->getAttribute('parentLineNumber');
            $priceValue        = $this->getElementValue('Price', $line);
            $priceType         = ($priceValue) ? $line->getElementsByTagName('Price')->item(0)->getAttribute('priceType') : '';
            $taxValue          = $this->getElementValue('Tax', $line);
            $taxPercent        = ($taxValue) ? $line->getElementsByTagName('Tax')->item(0)->getAttribute('taxPercent') : '';
            $lineStatusElement = $line->getElementsByTagName('LineStatus');
            $status            = null;
            $statusDate        = null;
            if ($lineStatusElement->length) {
                $status     = $lineStatusElement->item(0)->nodeValue;
                $statusDate = $lineStatusElement->item(0)->getAttribute('date');
            }
            $quantityValue            = $this->getElementValue('Quantity', $line);
            $quantityElement          = $line->getElementsByTagName('Quantity');
            $includedBonusQuantity    = ($quantityElement->length) ? $includedBonusQuantity = $quantityElement->item(0)->getAttribute('bonus') : '';
            $this->lines[$lineNumber] = array(
                'lineNumber' => $lineNumber,
                'itemCode' => $this->getElementValue('ItemCode', $line),
                'productName' => $this->getElementValue('ProductName', $line),
                'quantity' => $quantityValue,
                'priceType' => $priceType,
                'price' => $priceValue,
                'effectiveUnitPrice' => $this->getElementValue('EffectiveUnitPrice', $line),
                'taxPercent' => $taxPercent,
                'tax' => $taxValue,
                'totalPrice' => $this->getElementValue('TotalPrice', $line),
                'status' => $status,
                'statusDate' => $statusDate,
                'info' => $this->getElementValue('Info', $line),
                'includedBonusQuantity' => $includedBonusQuantity,
                'options' => array(
                    'dimensionLabel1' => $this->getElementValue('DimensionLabel1', $line),
                    'dimensionValue1' => $this->getElementValue('DimensionValue1', $line),
                    'dimensionLabel2' => $this->getElementValue('DimensionLabel2', $line),
                    'dimensionValue2' => $this->getElementValue('DimensionValue2', $line),
                    'dimensionLabel3' => $this->getElementValue('DimensionLabel3', $line),
                    'dimensionValue3' => $this->getElementValue('DimensionValue3', $line)
                ),
                'parentLineNumber' => $parentLineNumber
            );
            $itemCustomizations       = $line->getElementsByTagName('CustomizationField');
            if ($itemCustomizations->length) {
                foreach ($itemCustomizations as $customizationField) {
                    $combinedCustomizationValue  = '';
                    $customizationValuesElements = $customizationField->getElementsByTagName('Value');
                    $values                      = $customizationValuesElements->length;
                    if ($values) {
                        $i = 1;
                        foreach ($customizationValuesElements as $value) {
                            $combinedCustomizationValue .= $value->nodeValue;
                            if ($i < $values) {
                                $combinedCustomizationValue . ",";
                            }
                            ++$i;
                        }
                    }
                    $this->lines[$lineNumber]['customizations'][] = array(
                        'customizationName' => $customizationField->getAttribute('fieldName'),
                        'customizationValue' => $combinedCustomizationValue
                    );
                }
            }
            $originalLineNumber       = $line->getAttribute('originalLineNumber');
            $path                     = "//LineItem[@lineNumber='" . $originalLineNumber . "']/ItemCode";
            $originalLineItemCode     = $this->_xpath->query($path);
            $originalItemReplacedCode = '';
            if ($originalLineItemCode->length) {
                $originalItemReplacedCode = $originalLineItemCode->item(0)->nodeValue;
            }
            $this->lines[$lineNumber]['originalItemReplaced'] = $originalItemReplacedCode;
            $parentLineNumber                                 = $line->getAttribute('parentLineNumber');
            $orderLineAdditionType                            = $line->getAttribute('orderLineAdditionType');
            switch ($orderLineAdditionType) {
                case '5':
                case '15':
                    $parentLineReason = 'Bundle';
                    break;
                case '6':
                case '16':
                    $parentLineReason = 'Standing Order';
                    break;
                case '7':
                case '17':
                    $parentLineReason = 'Marketing Kit';
                    break;
                default:
                    $parentLineReason = '';
                    break;
            }
            $this->lines[$lineNumber]['parentLineReason'] = $parentLineReason;
            $standingOrderInfo                            = $line->getElementsByTagName('StandingOrder');
            if ($standingOrderInfo->length) {
                $this->lines[$lineNumber]['continuityConfigurationID']   = $standingOrderInfo->item(0)->getAttribute('configurationID');
                $this->lines[$lineNumber]['continuityConfigurationName'] = $standingOrderInfo->item(0)->getAttribute('configurationName');
                $this->lines[$lineNumber]['continuityShipmentID']        = $standingOrderInfo->item(0)->getAttribute('recurrenceCount');
            }
            $this->lines[$lineNumber]['waitDate'] = $this->getElementValue('WaitDate', $line);
        }
        $this->is_loaded = true;
    }
    private function getElementValue($elementName, DOMElement $parent = null)
    {
        $element = (!$parent instanceof DOMElement) ? $this->_xml->getElementsByTagName($elementName) : $parent->getElementsByTagName($elementName);
        if ($element instanceof DOMNodeList) {
            if ($element->length) {
                return $element->item(0)->nodeValue;
            }
        }
        return false;
    }
    public function cancelItems($lines = array())
    {
        $cancelLines = array();
        foreach ($lines as $lineNumber => $line) {
            if (array_key_exists($lineNumber, $this->lines)) {
                $cancelLines[$lineNumber] = $lines[$lineNumber];
            }
        }
        if (!count($lines)) {
            foreach ($this->lines as $line) {
                $cancelLines[$line['lineNumber']] = array(
                    'lineNumber' => $line['lineNumber'],
                    'cancelQuantity' => $line['quantity'],
                    'cancelReason' => '1'
                );
            }
        }
        $requestXml = Mage::getModel('hooks/xml_order_cancellation_request', array(
            $this,
            $cancelLines
        ));
        if ($requestXml instanceof Omx_Hooks_Model_Xml_Order_Cancellation_Request) {
            $credentials = array(
                'xml' => $requestXml
            );
            $connector   = Mage::getModel('hooks/connector');
            $result      = $connector->callOmx(Omx_Hooks_Model_Connector::CALL_TYPE_OCR, $credentials);
            return $result;
        }
        return false;
    }

    public function postponeItems($originalData)
    {
        $parsedData  = array();
        $partialData = array();
        $finalData   = array();
        if (count($this->lines)) {
            foreach ($this->lines as $orderline) {
                if (array_key_exists($orderline['lineNumber'], $originalData)) {
                    $partialData[$originalData[$orderline['lineNumber']]['newWaitDate']][] = $orderline['lineNumber'];
                }
            }
        }
        foreach ($partialData as $date => $linesArray) {
            $itemsCount = count($linesArray);
            if ($itemsCount > 1) {
                for ($i = 0; $i < $itemsCount; $i++) {
                    $separator = ($i != ($itemsCount - 1)) ? ',' : '';
                    if (!$i) {
                        $parsedData[$date] = '';
                    }
                    $parsedData[$date] .= $linesArray[$i] . $separator;
                }
            } else {
                $parsedData[$date] = $linesArray[0];
            }
        }
        foreach ($parsedData as $date => $lines) {
            $finalData[] = array(
                'lineNumberList' => $lines,
                'newWaitDate' => $date
            );
        }
        $connector = Mage::getModel('hooks/connector');
        $result    = array();
        foreach ($finalData as $postponeData) {
            $requestXml = Mage::getModel('hooks/xml_order_delay_request', array(
                $this,
                $postponeData
            ));
            if ($requestXml instanceof Omx_Hooks_Model_Xml_Order_Delay_Request) {
                $credentials                             = array(
                    'xml' => $requestXml
                );
                $result[$postponeData['lineNumberList']] = $connector->callOmx(Omx_Hooks_Model_Connector::CALL_TYPE_OWDUR, $credentials);
            }
        }
        return $result;
    }

    public function modifyItems($lines)
    {
        $modifyLines = array();
        foreach ($lines as $lineNumber => $line) {
            if (array_key_exists($lineNumber, $this->lines)) {
                if (array_key_exists('quantity', $line)) {
                    $previousValue = array_key_exists('quantity', $this->lines[$lineNumber]) ? $this->lines[$lineNumber]['quantity'] : '';
                    if ($line['quantity'] != $previousValue)
                        $modifyLines[$lineNumber]['quantity'] = $line['quantity'];
                }
                if (array_key_exists('standingOrder', $line)) {
                    $previousValue = array_key_exists('continuityConfigurationName', $this->lines[$lineNumber]) ? $this->lines[$lineNumber]['continuityConfigurationName'] : '';
                    if ($line['standingOrder'] != $previousValue) {
                        $modifyLines[$lineNumber]['standingOrder'] = $line['standingOrder'];
                        if (!array_key_exists('quantity', $modifyLines[$lineNumber]))
                            $modifyLines[$lineNumber]['quantity'] = $this->lines[$lineNumber]['quantity'];
                    }
                }
            }
        }
        if (count($modifyLines)) {
            $requestXml = Mage::getModel('hooks/xml_order_modification_request', array(
                $this,
                $modifyLines
            ));
            if ($requestXml instanceof Omx_Hooks_Model_Xml_Order_Modification_Request) {
                $credentials = array(
                    'xml' => $requestXml
                );
                $connector   = Mage::getModel('hooks/connector');
                $result      = $connector->callOmx(Omx_Hooks_Model_Connector::CALL_TYPE_ODUR, $credentials);
                return $result;
            }
            return false;
        }
        return true;
    }
    public function addItems($lines)
    {
        $addLines = array();
        foreach ($lines as $lineNumber => $line) {
            if (array_key_exists('sku', $line)) {
                $addLines[$lineNumber]['sku'] = $line['sku'];
                if (array_key_exists('quantity', $line))
                    $addLines[$lineNumber]['quantity'] = $line['quantity'];
                if (array_key_exists('standingOrder', $line))
                    $addLines[$lineNumber]['standingOrder'] = $line['standingOrder'];
                if (array_key_exists('unitPrice', $line))
                    $addLines[$lineNumber]['unitPrice'] = $line['unitPrice'];
            }
        }
        if (count($addLines)) {
            $requestXml = Mage::getModel('hooks/xml_order_appending_lines_request', array(
                $this,
                $addLines
            ));
            if ($requestXml instanceof Omx_Hooks_Model_Xml_Order_Appending_Lines_Request) {
                $credentials = array(
                    'xml' => $requestXml
                );
                $connector   = Mage::getModel('hooks/connector');
                $result      = $connector->callOmx(Omx_Hooks_Model_Connector::CALL_TYPE_ODUR, $credentials);
                return $result;
            }
            return false;
        }
        return true;
    }
}
