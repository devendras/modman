<?php

class Omx_Hooks_Model_Xml_Order_Appending_Request extends Omx_Hooks_Model_Xml_Abstract
{
    protected $_order;
    protected $_root;
    protected $_fullOrder;
    protected $_isMultiAddressShipping = 0;
    protected $_customer = false;
    public function __construct(Mage_Sales_Model_Order $order)
    {
        parent::__construct();
        $this->createRootElement();
        $this->_order = $order;
        $quote        = $order->getQuote();
        if ($quote instanceof Mage_Sales_Model_Quote) {
            if ($quote->getIsMultiShipping() == 1 || $quote->getIsMultiShipping() == true) {
                $this->_isMultiAddressShipping = 1;
            }
        }
        if (!$this->_order->getCustomerIsGuest()) {
            $customerId      = $this->_order->getCustomerId();
            $this->_customer = Mage::getModel('customer/customer')->load($customerId);
        }
        $this->_fullOrder = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('*')->addFilter("entity_id", $order->getId())->getFirstItem();
        $exteriorOrder    = null;
        $exteriorOrder    = $order->getExtOrderId();
        if ($this->_fullOrder->getId() || $exteriorOrder) {
            if ($exteriorOrder) {
                $this->_fullOrder = $this->_order;
            }
            $this->appendUdiParams();
            $this->appendHeader();
            $this->appendCustomer();
            $this->appendShipping();
            $this->appendPayment();
            $this->fixOmxPaymentBug();
            $this->appendItems();
            $this->appendCustomFields();
            $this->appendCheck();
            $this->appendFreeItemSelections();
        } else {
            $fullOrderErrorData = array(
                'request' => 'get full order data for:' . $order->getId(),
                'response' => 'order object was not found or it failed loading the attributes or is malformed by 3rd party',
                'comment' => 'this should not happen unless something goes wrong with custom attributes loading'
            );
            $this->log($fullOrderErrorData);
            return false;
        }
    }
    protected function createRootElement()
    {
        $rootElement        = $this->createSafeElement('UDOARequest');
        $rootVersionElement = $this->xml->createAttribute('version');
        $rootElement->appendChild($rootVersionElement);
        $rootVersionValue = $this->xml->createTextNode('2.00');
        $rootVersionElement->appendChild($rootVersionValue);
        $this->xml->appendChild($rootElement);
        $this->_root = $rootElement;
    }
    protected function appendUdiParams()
    {
        $udiParameter            = $this->_root->appendChild($this->createSafeElement('UDIParameter'));
        $verifyFlagElement       = $this->createSafeElement('Parameter', 'False');
        $verifyFlagAttributeText = $this->xml->createTextNode('VerifyFlag');
        $verifyFlagKeyAttribute  = $this->xml->createAttribute('key');
        $verifyFlagKeyAttribute->appendChild($verifyFlagAttributeText);
        $verifyFlagElement->appendChild($verifyFlagKeyAttribute);
        $udiParameter->appendChild($verifyFlagElement);
        $queueFlagElement       = $this->createSafeElement('Parameter', 'True');
        $queueFlagAttributeText = $this->xml->createTextNode('QueueFlag');
        $queueFlagAttribute     = $this->xml->createAttribute('key');
        $queueFlagAttribute->appendChild($queueFlagAttributeText);
        $queueFlagElement->appendChild($queueFlagAttribute);
        $udiParameter->appendChild($queueFlagElement);
        $keycode              = ($this->_fullOrder->getOmxOrderKeycode()) ? $this->_fullOrder->getOmxOrderKeycode() : Mage::getModel('hooks/connector')->defaultKeycode;
        $keyCodeElement       = $this->createSafeElement('Parameter', $keycode);
        $keycodeAttributeText = $this->xml->createTextNode('Keycode');
        $keycodeAttribute     = $this->xml->createAttribute('key');
        $keycodeAttribute->appendChild($keycodeAttributeText);
        $keyCodeElement->appendChild($keycodeAttribute);
        $udiParameter->appendChild($keyCodeElement);
        $vendor              = ($this->_fullOrder->getOmxOrderVendor()) ? $this->_fullOrder->getOmxOrderVendor() : '';
        $vendorElement       = $this->createSafeElement('Parameter', $vendor);
        $vendorAttributeText = $this->xml->createTextNode('Vendor');
        $vendorAttribute     = $this->xml->createAttribute('key');
        $vendorAttribute->appendChild($vendorAttributeText);
        $vendorElement->appendChild($vendorAttribute);
        $udiParameter->appendChild($vendorElement);
        $callToAction              = ($this->_fullOrder->getOmxOrderCallToAction()) ? $this->_fullOrder->getOmxOrderCallToAction() : '';
        $callToActionElement       = $this->createSafeElement('Parameter', $callToAction);
        $callToActionAttributeText = $this->xml->createTextNode('CallToAction');
        $callToActionAttribute     = $this->xml->createAttribute('key');
        $callToActionAttribute->appendChild($callToActionAttributeText);
        $callToActionElement->appendChild($callToActionAttribute);
        $udiParameter->appendChild($callToActionElement);
    }
    protected function appendHeader()
    {
        $headerElement = $this->_root->appendChild($this->createSafeElement('Header'));
        $now           = Zend_Date::now();
        $now->setTimezone('America/New_York');
        $orderDateElement = $this->createSafeElement('OrderDate', $now->toString("yyyy-MM-dd HH:mm:ss"));
        $headerElement->appendChild($orderDateElement);
        $originTypeElement = $this->createSafeElement('OriginType', '3');
        $headerElement->appendChild($originTypeElement);
        $customStoreCode  = $this->_order->getOmxOrderStorecode();
        $storeCodeElement = $this->createSafeElement('StoreCode', (is_null($customStoreCode) Or strlen($customStoreCode) == 0) ? Mage::getModel('hooks/connector')->storeCode : $customStoreCode);
        $headerElement->appendChild($storeCodeElement);
        $orderIdElement = $this->createSafeElement('OrderID', $this->_order->getIncrementId());
        $headerElement->appendChild($orderIdElement);
        $customerIpElement = $this->createSafeElement('CustomerIP', $this->_order->getRemoteIp());
        $headerElement->appendChild($customerIpElement);
    }
    protected function appendCustomer()
    {
        $billing                = $this->_order->getBillingAddress();
        $billingCustomerName    = $billing->getFirstname();
        $billingCustomerSurname = $billing->getLastname();
        $paymentMethod          = $this->_order->getPayment()->getMethod();
        if (($paymentMethod == "paypal_standard" Or $paymentMethod == "paypal_express" Or $paymentMethod == "paypal_billing_agreement") And (($billing->getCountry() == "" Or $billing->getCountry() === null) Or ($billing->getCountry() == "US" And ($billing->getPostcode() == "" Or $billing->getPostcode() === null)) Or ($billing->getCountry() == "US" And ($billing->getRegionCode() == "" Or $billing->getRegionCode() === null)) Or ($billing->getStreet1() == "" Or $billing->getStreet1() === null))) {
            if ($this->_order->getShippingAddress() instanceof Mage_Sales_Model_Order_Address) {
                $billing = $this->_order->getShippingAddress();
            }
        }
        $customerElement = $this->_root->appendChild($this->createSafeElement('Customer'));
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            $customerNumberAttribute      = $this->xml->createAttribute('customerNumber');
            $omx_customer_number          = ($this->_customer->getOmxCustomerNumber()) ? $this->_customer->getOmxCustomerNumber() : '';
            $customerNumberAttributeValue = $this->xml->createTextNode($omx_customer_number);
            $customerNumberAttribute->appendChild($customerNumberAttributeValue);
            $customerElement->appendChild($customerNumberAttribute);
        }
        $addressElement        = $this->createSafeElement('Address');
        $addressAttribute      = $this->xml->createAttribute('type');
        $addressAttributeValue = $this->xml->createTextNode('BillTo');
        $addressAttribute->appendChild($addressAttributeValue);
        $addressElement->appendChild($addressAttribute);
        $customerElement->appendChild($addressElement);
        $countryCode   = $billing->getCountry();
        $countryCode   = ('GB' === $countryCode) ? "UK" : $countryCode;
        $address2Value = $billing->getStreet2();
        $countyName    = NULL;
        switch (strtoupper($countryCode)) {
            case 'AU':
            case 'BR':
            case 'CA':
            case 'CU':
            case 'IN':
            case 'MX':
            case 'US':
                $stateCode = substr($billing->getRegionCode(), 0, 10);
                break;
            case 'UK':
                $stateCode  = '';
                $countyName = $billing->getRegion();
                break;
            default:
                $stateCode     = '';
                $address2Value = $address2Value . ' ' . $billing->getRegion();
                break;
        }
        $titleCodeElement = $this->createSafeElement('TitleCode', Omx_Hooks_Model_Helper::getPrefixCode($billing->getPrefix()));
        $addressElement->appendChild($titleCodeElement);
        $companyElement = $this->createSafeElement('Company', $billing->getCompany());
        $addressElement->appendChild($companyElement);
        $firstnameElement = $this->createSafeElement('Firstname', $billingCustomerName);
        $addressElement->appendChild($firstnameElement);
        $lastnameElement = $this->createSafeElement('Lastname', $billingCustomerSurname);
        $addressElement->appendChild($lastnameElement);
        $address1 = $this->createSafeElement('Address1', $billing->getStreet1());
        $addressElement->appendChild($address1);
        $address2 = $this->createSafeElement('Address2', $address2Value);
        $addressElement->appendChild($address2);
        $city = $this->createSafeElement('City', $billing->getCity());
        $addressElement->appendChild($city);
        if ($countyName !== NULL) {
            $county = $this->createSafeElement('County', $countyName);
            $addressElement->appendChild($county);
        }
        $state = $this->createSafeElement('State', $stateCode);
        $addressElement->appendChild($state);
        $zip = $this->createSafeElement('ZIP', $billing->getPostcode());
        $addressElement->appendChild($zip);
        $country = $this->createSafeElement('TLD', $countryCode);
        $addressElement->appendChild($country);
        $phoneNumber = $this->createSafeElement('PhoneNumber', $billing->getTelephone());
        $addressElement->appendChild($phoneNumber);
        $email = $this->createSafeElement('Email', $this->_order->getCustomerEmail());
        $addressElement->appendChild($email);
        $user     = Mage::getSingleton('customer/session')->getCustomer();
        $flagData = $this->createSafeElement('FlagData');
        $customerElement->appendChild($flagData);
        if ($user instanceof Mage_Customer_Model_Customer) {
            $doNotEmail = 'False';
            $subscriber = Mage::getSingleton('newsletter/subscriber')->loadByCustomer($user);
            if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
                $doNotEmail              = ($subscriber->isSubscribed()) ? 'False' : 'True';
                $flagEmail               = $this->createSafeElement('Flag', $doNotEmail);
                $flagEmailAttribute      = $this->xml->createAttribute('name');
                $flagEmailAttributeValue = $this->xml->createTextNode('DoNotEmail');
                $flagEmailAttribute->appendChild($flagEmailAttributeValue);
                $flagEmail->appendChild($flagEmailAttribute);
                $flagData->appendChild($flagEmail);
            }
            $omxTaxExempt = $user->getOmxTaxExempt();
            if ($omxTaxExempt !== null) {
                $flagTaxEx               = $this->createSafeElement('Flag', $omxTaxExempt ? 'True' : 'False');
                $flagTaxExAttribute      = $this->xml->createAttribute('name');
                $flagTaxExAttributeValue = $this->xml->createTextNode('Business');
                $flagTaxExAttribute->appendChild($flagTaxExAttributeValue);
                $flagTaxEx->appendChild($flagTaxExAttribute);
                $flagData->appendChild($flagTaxEx);
            }
        }
    }
    protected function appendShipping()
    {
        $shipping            = $this->_order->getShippingAddress();
        $shippingInformation = $this->_root->appendChild($this->createSafeElement('ShippingInformation'));
        if (!is_null($shipping) && $shipping) {
            $paymentMethod = $this->_order->getPayment()->getMethod();
            if ($paymentMethod == "paypal_standard" Or $paymentMethod == "paypal_express" Or $paymentMethod == "paypal_billing_agreement") {
                $shipingCustomerName    = $shipping->getFirstname();
                $shipingCustomerSurname = $shipping->getLastname();
                if ($shipingCustomerSurname == "" Or $shipingCustomerSurname === null) {
                    if (strpos($shipingCustomerName, " ")) {
                        $shipingCustomerSurname = substr($shipingCustomerName, strpos($shipingCustomerName, " ") + 1);
                        $shipingCustomerName    = substr($shipingCustomerName, 0, strpos($shipingCustomerName, " "));
                        $shipping->setFirstname($shipingCustomerName);
                        $shipping->setLastname($shipingCustomerSurname);
                    }
                }
            }
            $address                   = $this->createSafeElement('Address');
            $typeAddressAttribute      = $this->xml->createAttribute('type');
            $typeAddressAttributeValue = $this->xml->createTextNode('ShipTo');
            $typeAddressAttribute->appendChild($typeAddressAttributeValue);
            $address->appendChild($typeAddressAttribute);
            $shippingInformation->appendChild($address);
            $shippingCountryCode   = $shipping->getCountry();
            $shippingCountryCode   = ('GB' === $shippingCountryCode) ? 'UK' : $shippingCountryCode;
            $shippingAddress2Value = $shipping->getStreet(2);
            $shippingCountyName    = NULL;
            switch (strtoupper($shippingCountryCode)) {
                case 'AU':
                case 'BR':
                case 'CA':
                case 'CU':
                case 'IN':
                case 'MX':
                case 'US':
                    $shippingStateCode = substr($shipping->getRegionCode(), 0, 10);
                    break;
                case 'UK':
                    $shippingStateCode  = '';
                    $shippingCountyName = $shipping->getRegion();
                    break;
                default:
                    $shippingStateCode     = '';
                    $shippingAddress2Value = $shippingAddress2Value . ' ' . $shipping->getRegion();
                    break;
            }
            $titleCode = $this->createSafeElement('TitleCode', Omx_Hooks_Model_Helper::getPrefixCode($shipping->getPrefix()));
            $address->appendChild($titleCode);
            $shippingFirstName = $this->createSafeElement('Firstname', $shipping->getFirstname());
            $address->appendChild($shippingFirstName);
            $shippingLastName = $this->createSafeElement('Lastname', $shipping->getLastname());
            $address->appendChild($shippingLastName);
            $shippingAddress1 = $this->createSafeElement('Address1', $shipping->getStreet(1));
            $address->appendChild($shippingAddress1);
            $shippingAddress2 = $this->createSafeElement('Address2', $shippingAddress2Value);
            $address->appendChild($shippingAddress2);
            $shippingCity = $this->createSafeElement('City', $shipping->getCity());
            $address->appendChild($shippingCity);
            if ($shippingCountyName !== NULL) {
                $shippingCounty = $this->createSafeElement('County', $shippingCountyName);
                $address->appendChild($shippingCounty);
            }
            $shippingState = $this->createSafeElement('State', $shippingStateCode);
            $address->appendChild($shippingState);
            $shippingZip = $this->createSafeElement('ZIP', $shipping->getPostcode());
            $address->appendChild($shippingZip);
            $shippingCountry = $this->createSafeElement('TLD', $shippingCountryCode);
            $address->appendChild($shippingCountry);
        }
        $shippingMethod = $this->_order->getShippingDescription();
        if (!is_null($shippingMethod) && $shippingMethod <> '') {
            $methodCode     = $this->createSafeElement("MethodName", $shippingMethod);
            $shippingAmount = $this->createSafeElement("ShippingAmount", $this->_order->getBaseShippingAmount());
            $isVirtual      = false;
        } else {
            $methodCode     = $this->createSafeElement("MethodName", Mage::getModel('hooks/connector')->shippingMethodNonShippable);
            $shippingAmount = $this->createSafeElement("ShippingAmount", 0);
            $isVirtual      = true;
        }
        if (Mage::helper('hooks/data')->getSettings('useOmxMarketingPolicies') == '1' && !$isVirtual && $this->_isMultiAddressShipping == 0) {
            $shipingAmountDiscountable = "True";
        } else {
            $shipingAmountDiscountable = "False";
        }
        $shippingAmountAttribteText = $this->xml->createTextNode($shipingAmountDiscountable);
        $shippingAmountAttribute    = $this->xml->createAttribute('discountable');
        $shippingAmountAttribute->appendChild($shippingAmountAttribteText);
        $shippingAmount->appendChild($shippingAmountAttribute);
        $shippingInformation->appendChild($methodCode);
        $shippingInformation->appendChild($shippingAmount);
        $handlingAmount = $this->createSafeElement("HandlingAmount", 0);
        $shippingInformation->appendChild($handlingAmount);
        $specialInstructions = $this->createSafeElement("SpecialInstructions", $this->_order->getOnestepcheckoutCustomercomment());
        $shippingInformation->appendChild($specialInstructions);
        $giftWrapping = $this->createSafeElement("GiftWrapping", "");
        $shippingInformation->appendChild($giftWrapping);
        $giftMessageId = $this->_order->getGiftMessageId();
        $giftMessage   = '';
        if (!is_null($giftMessageId)) {
            $messageObject = Mage::getModel('giftmessage/message');
            $messageObject->load((int) $giftMessageId);
            $giftMessage .= "Sender: " . $messageObject->getData('sender') . "\n\n";
            $giftMessage .= "Recipient:" . $messageObject->getData('recipient') . "\n\n";
            $giftMessage .= "Message:" . $messageObject->getData('message');
        }
        $giftMessage = $this->createSafeElement("GiftMessage", $giftMessage);
        $shippingInformation->appendChild($giftMessage);
    }
    protected function appendPayment()
    {
        $paymentData      = $this->_order->getPayment();
        $paymentTag       = $this->createSafeElement('Payment');
        $paymentAttribute = $this->xml->createAttribute('type');
        $paymentMethod    = $paymentData->getMethod();
        switch ($paymentMethod) {
            case 'free':
                $paymentType = 0;
                break;
            case 'ccsave':
            case 'authorizenet':
            case 'verisign':
            case 'paypal_direct':
            case 'paypaluk_direct':
                $paymentType = 1;
                break;
            case 'checkmo':
                $paymentType = 6;
                break;
            case 'googlecheckout':
                $paymentType = 8;
                break;
            case 'paypal_standard':
            case 'paypal_express':
            case 'paypaluk_express':
            case 'paypal_billing_agreement':
                $paymentType = 13;
                break;
            default:
                $paymentType = 'third_party';
                break;
        }
        if ($paymentType !== 'third_party') {
            $paymentAttributeValue = $this->xml->createTextNode($paymentType);
            $paymentAttribute->appendChild($paymentAttributeValue);
            $paymentTag->appendChild($paymentAttribute);
            $paymentElement = $this->_root->appendChild($paymentTag);
        }
        if ($paymentType === 1) {
            if ($paymentMethod === 'paypal_direct') {
                $cardNumber = $this->createSafeElement('CardNumber', 'Payment method "Paypal Direct" NOT SUPPORTED');
                $paymentElement->appendChild($cardNumber);
            } else {
                $ccNumber = $paymentData->getCcNumber();
                if (!$ccNumber) {
                    $ccNumber = $paymentData->getOmxOrderPaymentCreditcardnumber();
                }
                $cardNumber = $this->createSafeElement('CardNumber', $ccNumber);
                $paymentElement->appendChild($cardNumber);
                $ccExpMonth = $paymentData->getCcExpMonth();
                if (!$ccExpMonth && is_array($paymentData->getAdditionalInformation())) {
                    $additionalInformation = $paymentData->getAdditionalInformation();
                    if (is_array($additionalInformation['authorize_cards'])) {
                        $authorizeCards = $additionalInformation['authorize_cards'];
                        $firstCard      = reset($authorizeCards);
                        if ($firstCard)
                            $ccExpMonth = $firstCard['cc_exp_month'];
                    }
                }
                $cardExpDateMonth = $this->createSafeElement('CardExpDateMonth', $ccExpMonth);
                $paymentElement->appendChild($cardExpDateMonth);
                $ccExpYear = $paymentData->getCcExpYear();
                if (!$ccExpYear && is_array($paymentData->getAdditionalInformation())) {
                    $additionalInformation = $paymentData->getAdditionalInformation();
                    if (is_array($additionalInformation['authorize_cards'])) {
                        $authorizeCards = $additionalInformation['authorize_cards'];
                        $firstCard      = reset($authorizeCards);
                        if ($firstCard)
                            $ccExpYear = $firstCard['cc_exp_year'];
                    }
                }
                $cardExpDateYear = $this->createSafeElement('CardExpDateYear', $ccExpYear);
                $paymentElement->appendChild($cardExpDateYear);
                $cardStatus              = null;
                $cardAuthorizationAmount = null;
                switch ($paymentMethod) {
                    case 'verisign':
                        $transaction = $paymentData->getCreatedTransaction();
                        if (!$transaction instanceof Mage_Sales_Model_Order_Payment_Transaction) {
                            $transaction = $paymentData->getTransaction($paymentData->getLastTransId());
                        }
                        if ($transaction->getTxnType() === 'capture') {
                            $cardStatus = $this->createSafeElement('CardStatus', 1);
                        } elseif ($transaction->getTxnType() === 'authorization') {
                            $cardStatus              = $this->createSafeElement('CardStatus', 11);
                            $cardAuthorizationAmount = $this->createSafeElement('CardAuthorizationAmount', $paymentData->getData('base_amount_authorized'));
                        } else {
                            $cardStatus = $this->createSafeElement('CardStatus');
                        }
                        break;
                    case 'authorizenet':
                        if ($paymentData->getAnetTransType() === 'AUTH_CAPTURE') {
                            $cardStatus = $this->createSafeElement('CardStatus', 1);
                        } elseif ($paymentData->getAnetTransType() === 'AUTH_ONLY') {
                            $cardStatus              = $this->createSafeElement('CardStatus', 11);
                            $cardAuthorizationAmount = $this->createSafeElement('CardAuthorizationAmount', $paymentData->getData('base_amount_authorized'));
                        } else {
                            $cardStatus = $this->createSafeElement('CardStatus');
                        }
                        break;
                    case 'free':
                    case 'ccsave':
                        break;
                    default:
                        $cardStatus = $this->createSafeElement('CardStatus');
                        break;
                }
                if ($cardStatus != null)
                    $paymentElement->appendChild($cardStatus);
                if ($cardAuthorizationAmount != null)
                    $paymentElement->appendChild($cardAuthorizationAmount);
                $cardAuthentificationCode = '000000';
                if ($paymentData->getCcApproval()) {
                    $cardAuthentificationCode = $paymentData->getCcApproval();
                }
                $cardAuthCode = $this->createSafeElement('CardAuthCode', $cardAuthentificationCode);
                $paymentElement->appendChild($cardAuthCode);
                $cardTransactionID = null;
                switch ($paymentMethod) {
                    case 'verisign':
                        $transactionID     = ($paymentData->getTrxtype() === 'A') ? $paymentData->getCcTransId() : $paymentData->getLastTransId();
                        $cardTransactionID = $this->createSafeElement('CardTransactionID', $transactionID);
                        break;
                    case 'authorizenet':
                        $transactionID = $paymentData->getLastTransId();
                        if (!$transactionID && is_array($paymentData->getAdditionalInformation())) {
                            $additionalInformation = $paymentData->getAdditionalInformation();
                            if (array_key_exists('authorize_cards', $additionalInformation) && is_array($additionalInformation['authorize_cards'])) {
                                $authorizeCards = $additionalInformation['authorize_cards'];
                                $firstCard      = reset($authorizeCards);
                                if (is_array($firstCard) && array_key_exists('last_trans_id', $firstCard))
                                    $transactionID = $firstCard['last_trans_id'];
                            }
                        }
                        $cardTransactionID = $this->createSafeElement('CardTransactionID', $transactionID);
                        break;
                    case 'free':
                    case 'ccsave':
                        break;
                    default:
                        $cardTransactionID = $this->createSafeElement('CardTransactionID');
                        break;
                }
                if ($cardTransactionID != null)
                    $paymentElement->appendChild($cardTransactionID);
            }
        } elseif ($paymentType === 13) {
            $additionalInformation = $paymentData->getAdditionalInformation();
            $isPaypalAuthorization = false;
            if (array_key_exists('paypal_pending_reason', $additionalInformation)) {
                $isPaypalAuthorization = $additionalInformation['paypal_pending_reason'] == 'authorization';
            }
            if (!$isPaypalAuthorization) {
                $paidAmount       = $paymentData->getBaseAmountPaid();
                $payPalpaidAmount = $this->createSafeElement('PaidAmount', $paidAmount);
                $paymentElement->appendChild($payPalpaidAmount);
            } else {
                $authAmount       = $paymentData->getBaseAmountAuthorized();
                $payPalauthAmount = $this->createSafeElement('AuthorizationAmount', $authAmount);
                $paymentElement->appendChild($payPalauthAmount);
            }
            $transactionID       = $paymentData->getLastTransId();
            $payPalTransactionID = $this->createSafeElement('TransactionID', $transactionID);
            $paymentElement->appendChild($payPalTransactionID);
            if (array_key_exists('paypal_payer_id', $additionalInformation)) {
                $payerID = $additionalInformation['paypal_payer_id'];
            } else {
                $payerID = false;
            }
            $payPalPayerID = $this->createSafeElement('PayerID', $payerID);
            $paymentElement->appendChild($payPalPayerID);
            $buyerEmail       = $this->_order->getCustomerEmail();
            $payPalBuyerEmail = $this->createSafeElement('BuyerEmail', $buyerEmail);
            $paymentElement->appendChild($payPalBuyerEmail);
            if ($isPaypalAuthorization) {
                $paypalTransactionType = $this->createSafeElement('PaypalTransactionType', 2);
                $paymentElement->appendChild($paypalTransactionType);
            }
            $billAgreement   = $paymentData->getBillingAgreement();
            $billAgreementId = "";
            if (isset($billAgreement) && $billAgreement instanceof Mage_Sales_Model_Billing_Agreement) {
                $billAgreementId = $billAgreement->getData('reference_id');
            } else if (array_key_exists('ba_reference_id', $additionalInformation)) {
                $billAgreementId = $additionalInformation['ba_reference_id'];
            } else if ($this->_customer && Mage::helper('hooks/data')->getSettings('sendPaypalBillingAgreementID') == 1) {
                $bam = Mage::getModel('sales/billing_agreement');
                if (isset($bam)) {
                    $baCollection = $bam->getAvailableCustomerBillingAgreements($this->_customer->getId());
                    if ($baCollection->count() > 0) {
                        $bat             = $baCollection->getFirstItem();
                        $billAgreementId = $bat->getData('reference_id');
                    }
                }
            }
            if ($billAgreementId != "") {
                $paypalBillingAgreement = $this->createSafeElement('BillingAgreementID', $billAgreementId);
                $paymentElement->appendChild($paypalBillingAgreement);
            }
        } elseif ($paymentType === 8) {
            $googleCheckoutOrderIdElement = $this->createSafeElement('CardTransactionID', $this->_order->getExtOrderId());
            $paymentElement->appendChild($googleCheckoutOrderIdElement);
            $paidAmount       = $paymentData->getData('base_amount_paid');
            $payPalpaidAmount = $this->createSafeElement('PaidAmount', $paidAmount);
            $paymentElement->appendChild($payPalpaidAmount);
        } elseif ($paymentType === 'third_party') {
            $paymentAttributeValue = $this->xml->createTextNode($paymentData->getOmxOrderPaymentType());
            $paymentAttribute->appendChild($paymentAttributeValue);
            $paymentTag->appendChild($paymentAttribute);
            $paymentElement = $this->_root->appendChild($paymentTag);
            switch ($paymentData->getOmxOrderPaymentType()) {
                case 0:
                    break;
                case 1:
                    $cardNumber = $this->createSafeElement('CardNumber', $paymentData->getOmxOrderPaymentCreditcardnumber());
                    $paymentElement->appendChild($cardNumber);
                    $cardExpDateMonth = $this->createSafeElement('CardExpDateMonth', $paymentData->getOmxOrderPaymentCreditcardexpdatemonth());
                    $paymentElement->appendChild($cardExpDateMonth);
                    $cardExpDateYear = $this->createSafeElement('CardExpDateYear', $paymentData->getOmxOrderPaymentCreditcardexpdateyear());
                    $paymentElement->appendChild($cardExpDateYear);
                    if (($cardTokenValue = $paymentData->getOmxOrderPaymentCreditcardtoken()) !== null) {
                        $cardToken = $this->createSafeElement('CardToken', $cardTokenValue);
                        $paymentElement->appendChild($cardToken);
                    }
                    if (($cardTypeValue = $paymentData->getOmxOrderPaymentCreditcardtype()) !== null) {
                        $cardType = $this->createSafeElement('CardType', $cardTypeValue);
                        $paymentElement->appendChild($cardType);
                    }
                    if (($cardFirst6DigitsValue = $paymentData->getOmxOrderPaymentCreditcardfirst6digits()) !== null) {
                        $cardFirst6Digits = $this->createSafeElement('CardFirst6Digits', $cardFirst6DigitsValue);
                        $paymentElement->appendChild($cardFirst6Digits);
                    }
                    if (($cardLast4DigitsValue = $paymentData->getOmxOrderPaymentCreditcardlast4digits()) !== null) {
                        $cardLast4Digits = $this->createSafeElement('CardLast4Digits', $cardLast4DigitsValue);
                        $paymentElement->appendChild($cardLast4Digits);
                    }
                    $cardAuthorizationAmount = null;
                    switch ($paymentData->getOmxOrderPaymentStatus()) {
                        case "pending":
                            $cardStatusValue = 0;
                            break;
                        case "charged":
                            $cardStatusValue = 1;
                            break;
                        case "authorized":
                            $cardStatusValue         = 11;
                            $cardAuthorizationAmount = $this->createSafeElement('CardAuthorizationAmount', $paymentData->getData('base_amount_authorized'));
                            break;
                        default:
                            $cardStatusValue = "";
                            break;
                    }
                    $cardStatus = $this->createSafeElement('CardStatus', $cardStatusValue);
                    $paymentElement->appendChild($cardStatus);
                    if ($cardAuthorizationAmount != null)
                        $paymentElement->appendChild($cardAuthorizationAmount);
                    if (($cardStatusValue === 1) or ($cardStatusValue === 11)) {
                        $cardAuthCode = $this->createSafeElement('CardAuthCode', $paymentData->getOmxOrderPaymentCreditcardauthcode());
                        $paymentElement->appendChild($cardAuthCode);
                    }
                    $cardTransactionID = $this->createSafeElement('CardTransactionID', $paymentData->getOmxOrderPaymentTransactionid());
                    $paymentElement->appendChild($cardTransactionID);
                    if (($cardAVSResultValue = $paymentData->getOmxOrderPaymentCreditcardavsresultcode()) !== null) {
                        $cardAVSResult = $this->createSafeElement('CardAVSResult', $cardAVSResultValue);
                        $paymentElement->appendChild($cardAVSResult);
                    }
                    if (($cardCCVResultValue = $paymentData->getOmxOrderPaymentCreditcardccvresultcode()) !== null) {
                        $cardCCVResult = $this->createSafeElement('CardCCVResult', $cardCCVResultValue);
                        $paymentElement->appendChild($cardCCVResult);
                    }
                    if (($cardIssueNumberValue = $paymentData->getOmxOrderPaymentCreditcardissuenumber()) !== null) {
                        $cardIssueNumber = $this->createSafeElement('CardIssueNumber', $cardIssueNumberValue);
                        $paymentElement->appendChild($cardIssueNumber);
                    }
                    if (($cardStartDateYearValue = $paymentData->getOmxOrderPaymentCreditcardissuedateyear()) !== null) {
                        $cardStartDateYear = $this->createSafeElement('CardStartDateYear', $cardStartDateYearValue);
                        $paymentElement->appendChild($cardStartDateYear);
                    }
                    if (($cardStartDateMonthValue = $paymentData->getOmxOrderPaymentCreditcardissuedatemonth()) !== null) {
                        $cardStartDateMonth = $this->createSafeElement('CardStartDateMonth', $cardStartDateMonthValue);
                        $paymentElement->appendChild($cardStartDateMonth);
                    }
                    break;
                case 2:
                case 3:
                case 4:
                    $paidAmount = $this->createSafeElement('PaidAmount', $paymentData->getOmxOrderPaymentPaidamount());
                    $paymentElement->appendChild($paidAmount);
                    if (($paymentData->getOmxOrderPaymentType() == 3) or ($paymentData->getOmxOrderPaymentType() == 4)) {
                        $checkNumber = $this->createSafeElement('CheckNumber', $paymentData->getOmxOrderPaymentChecknumber());
                        $paymentElement->appendChild($checkNumber);
                    }
                    break;
                case 5:
                case 6:
                    break;
                case 7:
                    switch ($paymentData->getOmxOrderPaymentStatus()->strtolower()) {
                        case "pending":
                            $checkStatusValue = 0;
                            break;
                        case "charged":
                            $checkStatusValue = 1;
                            break;
                        case "authorized":
                            $checkStatusValue = 11;
                            break;
                    }
                    $checkStatus = $this->createSafeElement('CheckStatus', $checkStatusValue);
                    $paymentElement->appendChild($checkStatus);
                    $cardTransactionID = $this->createSafeElement('CardTransactionID', $paymentData->getOmxOrderPaymentTransactionid());
                    $paymentElement->appendChild($cardTransactionID);
                    $bankAccountNumber = $this->createSafeElement('BankAccountNumber', $paymentData->getOmxOrderPaymentEcheckbankaccountnumber());
                    $paymentElement->appendChild($bankAccountNumber);
                    $bankRoutingNumber = $this->createSafeElement('BankRoutingNumber', $paymentData->getOmxOrderPaymentEcheckbankroutingnumber());
                    $paymentElement->appendChild($bankRoutingNumber);
                    break;
                case 8:
                    $paidAmount = $this->createSafeElement('PaidAmount', $paymentData->getOmxOrderPaymentPaidamount());
                    $paymentElement->appendChild($paidAmount);
                    break;
                case 10:
                    break;
                case 11:
                    $paidAmount = $this->createSafeElement('PaidAmount', round($paymentData->getOmxOrderPaymentPaidamount(), 2));
                    $paymentElement->appendChild($paidAmount);
                    $giftCertificateCode = $this->createSafeElement('GiftCertificateCode', $paymentData->getOmxOrderPaymentGiftcertificatecode());
                    $paymentElement->appendChild($giftCertificateCode);
                    break;
                case 13:
                    $transactionID = $this->createSafeElement('TransactionID', $paymentData->getOmxOrderPaymentTransactionid());
                    $paymentElement->appendChild($transactionID);
                    if (($payerIDValue = $paymentData->getOmxOrderPaymentPaypalpayerid()) !== null) {
                        $payerID = $this->createSafeElement('PayerID', $payerIDValue);
                        $paymentElement->appendChild($payerID);
                    }
                    if (($buyerEmailValue = $paymentData->getOmxOrderPaymentPaypalbuyeremail()) !== null) {
                        $buyerEmail = $this->createSafeElement('BuyerEmail', $buyerEmailValue);
                        $paymentElement->appendChild($buyerEmail);
                    }
                    $paidAmount = $this->createSafeElement('PaidAmount', $paymentData->getOmxOrderPaymentPaidamount());
                    $paymentElement->appendChild($paidAmount);
                    break;
            }
        }
        $ppId = $this->_order->getOmxOrderPaymentplanId();
        if (is_int($ppId) && $ppId > 0) {
            $paymentplan = $this->createSafeElement('PaymentPlanID', $ppId);
            $paymentElement->appendChild($paymentplan);
        }
        $externGiftCert      = "16";
        $giftCertificateInfo = unserialize($this->_fullOrder->getData('gift_cards'));
        if (sizeof($giftCertificateInfo)) {
            if ($paymentMethod == 'free') {
                $paymentElement->setAttribute('type', $externGiftCert);
                $paidAmountElement = $this->createSafeElement('PaidAmount', round($giftCertificateInfo[0]['ba'], 2));
                $paymentElement->appendChild($paidAmountElement);
                $giftCertificateCodeElement = $this->createSafeElement('GiftCertificateCode', $giftCertificateInfo[0]['c']);
                $paymentElement->appendChild($giftCertificateCodeElement);
                unset($giftCertificateInfo[0]);
            }
            if (count($giftCertificateInfo)) {
                $additionalPaymentElement = $this->createSafeElement('AdditionalPayment');
                $paymentElement->appendChild($additionalPaymentElement);
                foreach ($giftCertificateInfo as $gc) {
                    $partialPaymentElement = $this->createSafeElement('Payment');
                    $partialPaymentElement->setAttribute('type', $externGiftCert);
                    $additionalPaymentElement->appendChild($partialPaymentElement);
                    $giftCertificateCodeElement = $this->createSafeElement('GiftCertificateCode', $gc['c']);
                    $partialPaymentElement->appendChild($giftCertificateCodeElement);
                    $paymentAmountElement = $this->createSafeElement('PaidAmount', round($gc['ba'], 2));
                    $partialPaymentElement->appendChild($paymentAmountElement);
                }
            }
        }
        $omxGc           = Mage::helper('hooks/giftcertificate')->getCertificates($this->_fullOrder);
        $baseOmxGcAmount = $this->_fullOrder->getBaseOmxGiftCertificatesAmountUsed();
        if ($baseOmxGcAmount > 0) {
            if ($paymentType === 0) {
                $paymentElement->setAttribute('type', '11');
                $paidAmountElement = $this->createSafeElement('PaidAmount', round($baseOmxGcAmount, 2));
                $paymentElement->appendChild($paidAmountElement);
                $giftCertificateCodeElement = $this->createSafeElement('GiftCertificateCode', $omxGc[0]['code']);
                $paymentElement->appendChild($giftCertificateCodeElement);
            } else {
                $additionalPaymentElement = $this->createSafeElement('AdditionalPayment');
                $paymentElement->appendChild($additionalPaymentElement);
                $partialPaymentElement = $this->createSafeElement('Payment');
                $partialPaymentElement->setAttribute('type', '11');
                $additionalPaymentElement->appendChild($partialPaymentElement);
                $giftCertificateCodeElement = $this->createSafeElement('GiftCertificateCode', $omxGc[0]['code']);
                $partialPaymentElement->appendChild($giftCertificateCodeElement);
                $paymentAmountElement = $this->createSafeElement('PaidAmount', round($baseOmxGcAmount, 2));
                $partialPaymentElement->appendChild($paymentAmountElement);
            }
        }
    }
    protected function fixOmxPaymentBug()
    {
        $paymentType       = null;
        $cardStatusValue   = null;
        $paidAmountElement = null;
        $paymentElement    = $this->xml->getElementsByTagName('Payment')->item(0);
        if ($paymentElement instanceof DOMNode) {
            $paymentType = $paymentElement->getAttribute('type');
            if ($paymentElement->getElementsByTagName('CardStatus')->length >= 1) {
                $cardStatusValue   = $paymentElement->getElementsByTagName('CardStatus')->item(0)->nodeValue;
                $cardStatusElement = $paymentElement->getElementsByTagName('CardStatus')->item(0);
            }
            if ((int) $paymentType == 1 && (int) $cardStatusValue == 1) {
                $baseAmountPaid    = $this->_order->getPayment()->getBaseAmountPaid();
                $additionalPayment = $paymentElement->getElementsByTagName('AdditionalPayment')->item(0);
                if (!$additionalPayment) {
                    $additionalPayment = $this->createSafeElement('AdditionalPayment');
                    $paymentElement->appendChild($additionalPayment);
                }
                $clonedPayment                = $paymentElement->cloneNode(true);
                $clonedAddionalPaymentElement = $clonedPayment->getElementsByTagName('AdditionalPayment')->item(0);
                if ($clonedAddionalPaymentElement instanceof DOMNode) {
                    $clonedPayment->removeChild($clonedAddionalPaymentElement);
                }
                if ($clonedPayment->getElementsByTagName('PaidAmount')->length >= 1) {
                    $paidAmountElement = $clonedPayment->getElementsByTagName('PaidAmount')->item(0);
                }
                if (!$paidAmountElement instanceof DOMNode) {
                    $clonedPayment->appendChild($this->createSafeElement('PaidAmount', $baseAmountPaid));
                } elseif (!$paidAmountElement->nodeValue) {
                    $clonedPayment->replaceChild($this->createSafeElement('PaidAmount', $baseAmountPaid), $paidAmountElement);
                }
                $additionalPayment->appendChild($clonedPayment);
                if ($cardStatusElement instanceof DOMNode) {
                    $paymentElement->replaceChild($this->createSafeElement('CardStatus'), $cardStatusElement);
                }
                if ($paidAmountElement instanceof DOMNode) {
                    $paymentElement->replaceChild($this->createSafeElement('PaidAmount'), $paidAmountElement);
                }
            }
        }
    }
    protected function appendItems()
    {
        $items          = $this->_order->getAllItems();
        $ruleIDs        = array();
        $parent_options = array();
        $orderDetail    = $this->_root->appendChild($this->createSafeElement('OrderDetail'));
        foreach ($items as $key => $item) {
            $parent_price = null;
            if ($item->getProductType() === "configurable") {
                $parent_price   = $item->getData('base_price');
                $parent_options = $item->getProductOptions();
            } elseif (in_array($item->getProductType(), array(
                "simple",
                "downloadable",
                "virtual",
                "giftcard"
            ))) {
                $lineItem = $this->createSafeElement('LineItem');
                $lineItem->setAttribute('externalLineReference', $item->getItemId());
                if ($item->getProductType() == "giftcard") {
                    $isMagentoGiftCardElement = $this->createSafeElement('IsMagentoGiftCardItem', 'True');
                    $lineItem->appendChild($isMagentoGiftCardElement);
                    $paidInvoices          = array();
                    $invoiceId             = null;
                    $invoice               = null;
                    $invoiceDate           = null;
                    $invoiceItemCollection = Mage::getResourceModel('sales/order_invoice_item_collection')->addFieldToFilter('order_item_id', $item->getId());
                    foreach ($invoiceItemCollection as $invoiceItem) {
                        $invoiceId = $invoiceItem->getParentId();
                        $invoice   = Mage::getModel('sales/order_invoice')->load($invoiceId);
                        if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID) {
                            $paidInvoices[$invoiceId] = array(
                                'amount' => $invoice->getBaseGrandTotal(),
                                'date' => $invoice->getUpdatedAt()
                            );
                        }
                    }
                    $paidAmount = 0;
                    ksort($paidInvoices, SORT_NUMERIC);
                    foreach ($paidInvoices as $id => $invoiceData) {
                        $paidAmount += $invoiceData['amount'];
                        $invoiceDate = $invoiceData['date'];
                    }
                    if ($paidAmount >= $this->_order->getBaseGrandTotal()) {
                        $isMagentoGiftCardElement->setAttribute('activationDate', $invoiceDate);
                    }
                }
                $itemCode = $this->createSafeElement('ItemCode', $item->getSku());
                $lineItem->appendChild($itemCode);
                $quantity = $this->createSafeElement('Quantity', round($item->getQtyOrdered()));
                $lineItem->appendChild($quantity);
                $itemOptions = $item->getProductOptions();
                if (is_array($parent_options) && ($item->getParentItemId() !== NULL)) {
                    $itemOptions = array_merge($itemOptions, $parent_options);
                }
                $itemCustomizationData = $this->createSafeElement('ItemCustomizationData');
                if (array_key_exists('options', $itemOptions)) {
                    $continuity = Mage::getModel('hooks/connector')->continuityCustomOptionName;
                    foreach ($itemOptions['options'] as $value) {
                        if ($value['label'] === $continuity) {
                            $configurationNameValue = $this->xml->createTextNode($value['value']);
                            $configurationName      = $this->xml->createAttribute('configurationName');
                            $configurationName->appendChild($configurationNameValue);
                            $standingOrder = $this->createSafeElement('StandingOrder');
                            $standingOrder->appendChild($configurationName);
                            $lineItem->appendChild($standingOrder);
                        } else {
                            $personalizationNameAttributeValue = $this->xml->createTextNode($value['label']);
                            $personalizationNameAttribute      = $this->xml->createAttribute("fieldName");
                            $personalizationNameAttribute->appendChild($personalizationNameAttributeValue);
                            $customizationFieldValue = $this->createSafeElement('Value', $value['value']);
                            $customizationField      = $this->createSafeElement('CustomizationField');
                            $customizationField->appendChild($personalizationNameAttribute);
                            $customizationField->appendChild($customizationFieldValue);
                            $itemCustomizationData->appendChild($customizationField);
                        }
                    }
                }
                $itemExtras = unserialize($item->getOmxExtraCustomizations());
                if (is_array($itemExtras)) {
                    foreach ($itemExtras as $extraKey => $extraValue) {
                        $personalizationNameAttributeValue = $this->xml->createTextNode($extraKey);
                        $personalizationNameAttribute      = $this->xml->createAttribute("fieldName");
                        $personalizationNameAttribute->appendChild($personalizationNameAttributeValue);
                        $customizationFieldValue = $this->createSafeElement('Value', $extraValue);
                        $customizationField      = $this->createSafeElement('CustomizationField');
                        $customizationField->appendChild($personalizationNameAttribute);
                        $customizationField->appendChild($customizationFieldValue);
                        $itemCustomizationData->appendChild($customizationField);
                    }
                }
                if ($itemCustomizationData->hasChildNodes())
                    $lineItem->appendChild($itemCustomizationData);
                $forceMagentoPrices = Mage::getModel('hooks/connector')->forceMagentoPrices;
                $priceElement       = null;
                if (($forceMagentoPrices) && ($item->getParentItemId() !== NULL)) {
                    $priceElement = $this->createSafeElement('UnitPrice', $parent_price);
                    $lineItem->appendChild($priceElement);
                } elseif (($forceMagentoPrices) || ($item->getProductType() == "giftcard")) {
                    $priceElement = $this->createSafeElement('UnitPrice', $item->getData('base_price'));
                    $lineItem->appendChild($priceElement);
                }
                if (Mage::helper('hooks/data')->getSettings('useOmxMarketingPolicies') == 1 && $item->getProductType() != "giftcard" && $this->_isMultiAddressShipping == 0 && $priceElement instanceof DOMElement) {
                    $priceElement->setAttribute('discountable', 'True');
                }
                $ppId = $item->getOmxPaymentplanId();
                if (is_int($ppId) && $ppId > 0) {
                    $paymentplan = $this->createSafeElement('PaymentPlanID', $ppId);
                    $lineItem->appendChild($paymentplan);
                }
                if ($item->getOmxWaitDate()) {
                    $waitDateElement = $this->createSafeElement("WaitDate", $item->getOmxWaitDate());
                    $lineItem->appendChild($waitDateElement);
                }
                $orderDetail->appendChild($lineItem);
            }
        }
        if ($this->_order->getAppliedRuleIds() !== NULL && $this->_order->getAppliedRuleIds() !== '') {
            $ruleIDs = explode(',', $this->_order->getAppliedRuleIds());
            if (count($ruleIDs) === 1) {
                $rule       = Mage::getModel('salesrule/rule')->load($ruleIDs[0]);
                $couponName = $rule->getName();
                $couponCode = $this->_order->getCouponCode();
                $lineItem   = $this->createSafeElement('LineItem');
                $lineItem->setAttribute('externalLineReference', 'discountCoupon');
                $itemCode = $this->createSafeElement('ItemCode', $couponName);
                $lineItem->appendChild($itemCode);
                $quantity = $this->createSafeElement('Quantity', 1);
                $lineItem->appendChild($quantity);
                $priceElement = $this->createSafeElement('UnitPrice', $this->_order->getData('base_discount_amount'));
                $lineItem->appendChild($priceElement);
                $infoElement = $this->createSafeElement('Info', 'Coupon code: ' . ($couponCode !== NULL && $couponCode !== '' ? $couponCode : 'No coupon code'));
                $lineItem->appendChild($infoElement);
                if (Mage::getModel('hooks/connector')->autoCreateCoupons) {
                    $autoCreateCoupon = $this->createSafeElement('AutoCreateItem');
                    $autoShip         = $this->createSafeElement('AutoShip', 1);
                    $autoCreateCoupon->appendChild($autoShip);
                    $inventoryType = $this->createSafeElement('InventoryType', 0);
                    $autoCreateCoupon->appendChild($inventoryType);
                    $productName = $this->createSafeElement('ProductName', $couponName);
                    $autoCreateCoupon->appendChild($productName);
                    $infoText = $this->createSafeElement('InfoText', 'Auto-created coupon item from Magento Coupon Code (Magento Rule Name): ' . $couponName);
                    $autoCreateCoupon->appendChild($infoText);
                    $lineItem->appendChild($autoCreateCoupon);
                }
                $orderDetail->appendChild($lineItem);
            } else {
                $couponName = 'CombinedCoupon';
                $couponCode = $this->_order->getCouponCode();
                sort($ruleIDs);
                if ($couponCode !== NULL && $couponCode !== '') {
                    $coupon        = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
                    $ruleID        = $coupon->getRuleId();
                    $key           = array_search($ruleID, $ruleIDs);
                    $ruleIDs[$key] = $ruleIDs[$key] . ' (coupon code-> ' . $couponCode . ')';
                }
                $couponCodes = implode(',', $ruleIDs);
                $lineItem    = $this->createSafeElement('LineItem');
                $itemCode    = $this->createSafeElement('ItemCode', $couponName);
                $lineItem->appendChild($itemCode);
                $quantity = $this->createSafeElement('Quantity', 1);
                $lineItem->appendChild($quantity);
                $priceElement = $this->createSafeElement('UnitPrice', $this->_order->getData('base_discount_amount'));
                $lineItem->appendChild($priceElement);
                $infoElement = $this->createSafeElement('Info', 'Coupon codes (Magento Rules IDs): ' . $couponCodes);
                $lineItem->appendChild($infoElement);
                if (Mage::getModel('hooks/connector')->autoCreateCoupons) {
                    $autoCreateCoupon = $this->createSafeElement('AutoCreateItem');
                    $autoShip         = $this->createSafeElement('AutoShip', 1);
                    $autoCreateCoupon->appendChild($autoShip);
                    $inventoryType = $this->createSafeElement('InventoryType', 0);
                    $autoCreateCoupon->appendChild($inventoryType);
                    $productName = $this->createSafeElement('ProductName', $couponName);
                    $autoCreateCoupon->appendChild($productName);
                    $infoText = $this->createSafeElement('InfoText', 'Auto-created coupon item from Magento combined coupons.');
                    $autoCreateCoupon->appendChild($infoText);
                    $lineItem->appendChild($autoCreateCoupon);
                }
                $orderDetail->appendChild($lineItem);
            }
        }
    }
    protected function appendCustomFields()
    {
        $customFieldsElement = $this->_root->appendChild($this->createSafeElement('CustomFields'));
        $reportElement       = $this->createSafeElement('Report');
        $customFieldsElement->appendChild($reportElement);
        $omxCustomAttributes = unserialize($this->_fullOrder->getOmxOrderCustomFields());
        if (is_array($omxCustomAttributes)) {
            foreach ($omxCustomAttributes as $key => $value) {
                $fieldElement            = $this->createSafeElement('Field', $value);
                $fieldNameAttribute      = $this->xml->createAttribute('fieldName');
                $fieldNameAttributeValue = $this->xml->createTextNode($key);
                $fieldNameAttribute->appendChild($fieldNameAttributeValue);
                $fieldElement->appendChild($fieldNameAttribute);
                $reportElement->appendChild($fieldElement);
            }
        }
    }
    protected function appendCheck()
    {
        $sendCheckElement = (Mage::helper('hooks')->getSettings('checkOrderTotalAmount') == 0) ? false : true;
        if ($sendCheckElement) {
            $totalAmountOMX = $this->_fullOrder->getBaseGrandTotal();
            $useOmxCerts    = (Mage::helper('hooks')->getSettings('useOmxGiftCertificates') == 1) ? true : false;
            switch ($useOmxCerts) {
                case true:
                    $giftCardsAmount = $this->_fullOrder->getBaseOmxGiftCertificatesAmountUsed();
                    break;
                case false:
                    $giftCardsAmount = $this->_fullOrder->getBaseGiftCardsAmount();
                    break;
            }
            if ($giftCardsAmount)
                $totalAmountOMX += $giftCardsAmount;
            $checkElement       = $this->_root->appendChild($this->createSafeElement('Check'));
            $totalAmountElement = $this->createSafeElement('TotalAmount', $totalAmountOMX);
            $checkElement->appendChild($totalAmountElement);
        }
    }
    protected function appendFreeItemSelections()
    {
        if (Mage::helper('hooks/data')->getSettings('useOmxMarketingPolicies') == 1) {
            $freeItems = Mage::helper('hooks/omxpricing')->getDiscounts($this->_fullOrder, Omx_Hooks_Model_Omxpricing_Calculator::OMX_FREE_ITEM_DISCOUNTS);
            if (array_key_exists('selections', $freeItems)) {
                $headerPolicies    = array();
                $orderLinePolicies = array();
                foreach ($freeItems['selections'] as $selection) {
                    $selectedItem = '';
                    $selectedKey  = $selection['policyId'] . '_' . $selection['lineReference'];
                    if (array_key_exists($selectedKey, $freeItems['selectedItems'])) {
                        $selectedItem = $freeItems['selectedItems'][$selectedKey];
                    }
                    if ($selection['lineReference'] == 'header') {
                        $headerPolicies[$selection['policyId']] = $selectedItem;
                    } else {
                        $orderLinePolicies[$selection['lineReference']][$selection['policyId']] = $selectedItem;
                    }
                }
                if (count($headerPolicies)) {
                    $headerFreeISElement = $this->createSafeElement('FreeItemSelection');
                    foreach ($headerPolicies as $policyId => $itemCode) {
                        $freeItemElement = $this->createSafeElement('FreeItem', $itemCode);
                        $freeItemElement->setAttribute('marketingPolicyID', $policyId);
                        $headerFreeISElement->appendChild($freeItemElement);
                    }
                    $header = $this->_root->getElementsByTagName('Header')->item(0);
                    $header->appendChild($headerFreeISElement);
                }
                $orderLines = $this->_root->getElementsByTagName('LineItem');
                foreach ($orderLines as $orderLine) {
                    $externalLineRef = $orderLine->getAttribute('externalLineReference');
                    if ($externalLineRef != 'discountCoupon') {
                        $quoteItemId = $this->_fullOrder->getItemById($externalLineRef)->getQuoteItemId();
                        if (array_key_exists($quoteItemId, $orderLinePolicies)) {
                            $orderLineFreeISElement = $this->createSafeElement('FreeItemSelection');
                            foreach ($orderLinePolicies[$quoteItemId] as $policyId => $itemCode) {
                                $freeItemElement = $this->createSafeElement('FreeItem', $itemCode);
                                $freeItemElement->setAttribute('marketingPolicyID', $policyId);
                                $orderLineFreeISElement->appendChild($freeItemElement);
                            }
                            $orderLine->appendChild($orderLineFreeISElement);
                        }
                    }
                }
            }
        }
    }
}
