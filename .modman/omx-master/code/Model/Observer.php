<?php

class Omx_Hooks_Model_Observer
{
    public $_moduleConfigured = false;

    public function __construct()
    {
        $this->_moduleConfigured = Mage::getModel('hooks/connector')->isConfigured();
    }

    public function updateTrackingNumbers($observer)
    {
        $event = $observer->getEvent();

        if (!$event) {
            return;
        }

        if (!$event->getOrders()) {
            return;
        }

        if ($this->_moduleConfigured) {
            Mage::helper('hooks')->addTrackingDataFromOmx($event->getOrders());
        }
    }

    protected function log($data)
    {
        $omx_now = Zend_Date::now();
        $omx_now->setTimezone('America/New_York');
        $data['timestamp'] = $omx_now->toString("yyyy-MM-dd HH:mm:ss");
        $log               = Mage::getModel('hooks/logs')->setData($data);
        try {
            $log->save();
            unset($data);
            return $log->getId();
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
}
